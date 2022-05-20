<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Models\Package;
use App\Models\User;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class InstallController extends Controller
{
    private $minPhpVersion = '7.2.5';
    private $extensions    = [
        'openssl',
        'pdo',
        'mbstring',
        'xml',
        'ctype',
        'gd',
        'tokenizer',
        'JSON',
        'bcmath',
        'exif',
        'cURL',
        'fileinfo',
        'zip',
    ];

    private $permissions = [
        'storage'           => '0777',
        'storage/app'       => '0777',
        'storage/framework' => '0777',
        'storage/logs'      => '0777',
        'bootstrap/cache'   => '0777',
    ];

    public function install_check(Request $request)
    {
        // Clear cache, routes, views
        Artisan::call('optimize:clear');

        $passed = true;

        // Permissions checker
        $results['permissions'] = [];
        foreach ($this->permissions as $folder => $permission) {
            $results['permissions'][] = [
                'folder'     => $folder,
                'permission' => substr(sprintf('%o', fileperms(base_path($folder))), -4),
                'required'   => $permission,
                'success'    => substr(sprintf('%o', fileperms(base_path($folder))), -4) >= $permission ? true : false,
            ];
        }

        // Extension checker
        $results['extensions'] = [];
        foreach ($this->extensions as $extension) {
            $results['extensions'][] = [
                'extension' => $extension,
                'success'   => extension_loaded($extension),
            ];
        }

        $results['extensions'][] = [
            'extension' => 'proc_open',
            'success'   => function_exists('proc_open'),
        ];

        // PHP version
        $results['php'] = [
            'installed' => PHP_VERSION,
            'required'  => $this->minPhpVersion,
            'success'   => version_compare(PHP_VERSION, $this->minPhpVersion) >= 0 ? true : false,
        ];

        // Pass check
        foreach ($results['permissions'] as $permission) {
            if ($permission['success'] == false) {
                $passed = false;
                break;
            }
        }

        foreach ($results['extensions'] as $extension) {
            if ($extension['success'] == false) {
                $passed = false;
                break;
            }
        }

        try {
            chmod(config('pilot.PATH_FFPROBE'), 0777);
            chmod(config('pilot.PATH_FFMPEG'), 0777);
            chmod(storage_path('upgrade'), 0777);
        } catch (\Exception $e) {}

        if ($results['php']['success'] == false) {
            $passed = false;
        }

        return view('install.database', compact(
            'results',
            'passed'
        ));

    }

    public function install_db(Request $request)
    {
        $request->validate([
            'APP_URL'     => 'required|url',
            'DB_HOST'     => 'required|string|max:50',
            'DB_PORT'     => 'required|numeric',
            'DB_DATABASE' => 'required|string|max:50',
            'DB_USERNAME' => 'required|string|max:50',
            'DB_PASSWORD' => 'nullable|string|max:50',
        ]);

        // Check DB connection
        try {

            $pdo = new \PDO(
                'mysql:host=' . $request->DB_HOST . ';port=' . $request->DB_PORT . ';dbname=' . $request->DB_DATABASE,
                $request->DB_USERNAME,
                $request->DB_PASSWORD, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                ]
            );

        } catch (\PDOException $e) {

            return redirect()->route('install.check')
                ->with('error', 'Database connection failed: ' . $e->getMessage());

        } catch (\Exception $e) {

            return redirect()->route('install.check')
                ->with('error', 'Database error: ' . $e->getMessage());

        }

        // Setup .env file
        try {

            Helper::setEnv([
                'APP_URL'     => strtolower(rtrim($request->APP_URL, '/')),
                'APP_ENV'     => 'production',
                'APP_DEBUG'   => 'false',
                'DB_HOST'     => '"' . $request->DB_HOST . '"',
                'DB_PORT'     => '"' . $request->DB_PORT . '"',
                'DB_DATABASE' => '"' . $request->DB_DATABASE . '"',
                'DB_USERNAME' => '"' . $request->DB_USERNAME . '"',
                'DB_PASSWORD' => '"' . $request->DB_PASSWORD . '"',
            ]);

        } catch (\Exception $e) {

            return redirect()->route('install.check')
                ->with('error', 'Can\'t save changes to .env file: ' . $e->getMessage());

        }

        return redirect()->route('install.setup');

    }

    public function setup()
    {
        // Application key
        try {

            Artisan::call('key:generate', ["--force" => true]);

        } catch (\Exception $e) {

            return redirect()->route('install.check')
                ->with('error', 'Can\'t generate application key: ' . $e->getMessage());

        }

        // Migrate
        try {

            Artisan::call('migrate', ["--force" => true]);

        } catch (\Exception $e) {

            return redirect()->route('install.check')
                ->with('error', 'Can\'t migrate database: ' . $e->getMessage());

        }

        return redirect()->route('install.administrator');
    }

    public function install_administrator()
    {
        return view('install.administrator');
    }

    public function install_finish(Request $request)
    {
        $request->validate([
            'code'     => 'required|size:36',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|same:password_confirmation',
        ]);

        // Check for license
        try {

            $client   = new GuzzleClient();
            $response = $client->request('GET', 'https://license.dmpilot.live', [
                'idn_conversion' => false,
                'verify'         => false,
                'http_errors'    => false,
                'query'          => [
                    'code'    => $request->input('code'),
                    'ip'      => $request->server('SERVER_ADDR'),
                    'url'     => config('app.url'),
                    'version' => config('pilot.version'),
                ],
            ]);

            $responseCode     = $response->getStatusCode();
            $responseContents = $response->getBody()->getContents();
            $responseJSON     = json_decode($responseContents, true);

            if ($responseCode == 200 && $responseJSON['result'] == 'success') {
                $license = base64_decode($responseJSON['license']);

                try {
                    $encrypted = Crypt::encrypt([
                        'license' => $license,
                        'code'    => $request->input('code'),
                    ]);

                    Storage::disk('local')->put('pilot.license', $encrypted);

                } catch (\Exception $e) {
                    return redirect()->route('install.administrator')->with('error', $responseJSON['message']);
                }
            } else {
                return redirect()->route('install.administrator')->with('error', $responseJSON['message']);
            }

        } catch (\Exception $e) {
            return redirect()->route('install.administrator')->with('error', $e->getMessage());
        }

        // Create admin account
        $user = User::create([
            'is_admin'        => true,
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'package_id'      => 3,
            'package_ends_at' => now()->addYears(10),
        ]);

        // Pre-loaded messages list
        $templates = __('templates');

        foreach ($templates['messages'] as $group => $messages) {

            $messages_list = $user->lists()->create([
                'type' => 'messages',
                'name' => $group,
            ]);

            foreach ($messages as $message) {

                $messages_list->items()->create([
                    'text' => $message,
                ]);

            }
        }

        // Pre-loaded users list
        $users_list = $user->lists()->create([
            'type' => 'users',
            'name' => 'Most followed accounts on Instagram',
        ]);

        foreach ($templates['users'] as $username) {

            $users_list->items()->create([
                'text' => $username,
            ]);

        }

        // Create packages
        $packages = [
            [
                'title'       => 'Starter',
                'price'       => 4.99,
                'interval'    => 'month',
                'settings'    => [
                    'storage'        => '64',
                    'accounts_count' => '1',
                    'messages_count' => '1000',
                    'lists'          => true,
                    'posts'          => true,
                    'messages-log'   => true,
                    'send-message'   => true,
                    'media-manager'  => true,
                ],
                'is_featured' => false,
            ],
            [
                'title'       => 'Captain',
                'price'       => 14.99,
                'interval'    => 'month',
                'settings'    => [
                    'storage'          => '512',
                    'accounts_count'   => '5',
                    'messages_count'   => '10000',
                    'lists'            => true,
                    'posts'            => true,
                    'autopilot'        => true,
                    'messages-log'     => true,
                    'send-message'     => true,
                    'media-manager'    => true,
                    'direct-messenger' => true,
                ],
                'is_featured' => true,
            ],
            [
                'title'       => 'Jet Pilot',
                'price'       => 29.99,
                'interval'    => 'month',
                'settings'    => [
                    'storage'          => '1024',
                    'accounts_count'   => '20',
                    'messages_count'   => '1000000',
                    'bot'              => true,
                    'rss'              => true,
                    'lists'            => true,
                    'posts'            => true,
                    'autopilot'        => true,
                    'messages-log'     => true,
                    'send-message'     => true,
                    'media-manager'    => true,
                    'direct-messenger' => true,
                ],
                'is_featured' => false,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }

        // Save installation
        touch(storage_path('installed'));

        return redirect()->route('landing')
            ->with('success', 'Installation finished successfully');

    }

    public function update_check(Request $request)
    {
        // Clear cache, routes, views
        Artisan::call('optimize:clear');

        $migrations = Storage::disk('migrations')->files();
        $migrations = collect(str_replace('.php', '', $migrations));
        $migrated   = collect(DB::table('migrations')->pluck('migration')->all());
        $pending    = $migrations->diff($migrated);
        $upToDate   = $pending->count() == 0 ? true : false;

        return view('install.update', compact(
            'upToDate'
        ));
    }

    public function update_finish(Request $request)
    {
        $migrations = Storage::disk('migrations')->files();
        $migrations = collect(str_replace('.php', '', $migrations));
        $migrated   = collect(DB::table('migrations')->pluck('migration')->all());
        $pending    = $migrations->diff($migrated);
        $upToDate   = $pending->count() == 0 ? true : false;

        if ($upToDate) {
            return redirect()->route('landing')
                ->with('success', 'No update needed');
        }

        // Migrate
        try {

            Artisan::call('migrate', ["--force" => true]);

            // Save installation
            touch(storage_path('installed'));

        } catch (\Exception $e) {

            return redirect()->route('update.check')
                ->with('error', 'Can\'t migrate database: ' . $e->getMessage());

        }

        return redirect()->route('landing')
            ->with('success', 'Update finished successfully');
    }
}
