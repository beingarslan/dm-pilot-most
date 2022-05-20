<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BotController extends Controller
{
    public function setup()
    {
        $accounts = Account::all();

        return view('bot.setup', compact(
            'accounts'
        ));
    }

    public function index(Request $request, Account $account)
    {
        $bot = Bot::with('qa')
            ->firstOrNew([
                'account_id' => $account->id,
            ],
                [
                    'welcome_text' => null,
                    'unknown_text' => null,
                    'email'        => $request->user()->email,
                    'is_active'    => true,
                    'qa'           => null,
                ]
            );

        return view('bot.bot', compact(
            'account',
            'bot'
        ));
    }

    public function update(Request $request, Account $account)
    {
        if ($request->has('restart')) {

            Artisan::call('cache:clear');

            return redirect()->route('bot.index', $account)
                ->with('success', __('Chat Bot restarted'));
        }

        $request->validate([
            'items.*.question' => 'required|string',
            'items.*.answer'   => 'required|string',
            'items'            => 'required|array',
            'email'            => 'email:rfc,filter',
        ]);

        if (!$request->filled('is_active')) {
            $request->request->add([
                'is_active' => false,
            ]);
        } else {
            $request->request->add([
                'is_active' => true,
            ]);
        }

        $bot = Bot::updateOrCreate([
            'user_id'    => $account->user_id,
            'account_id' => $account->id,
        ], [
            'welcome_text' => $request->welcome_text,
            'unknown_text' => $request->unknown_text,
            'email'        => $request->email,
            'is_active'    => $request->is_active,
        ]);

        $bot->qa()->delete();

        $ordering = 1;

        foreach ($request->items as $qa) {

            $bot->qa()->create([
                'ordering'     => $ordering++,
                'hears'        => explode(',', $qa['question']),
                'message_type' => config('pilot.MESSAGE_TYPE_TEXT'),
                'message'      => [
                    'text' => $qa['answer'],
                ],
            ]);
        }

        return redirect()->route('bot.index', $account)
            ->with('success', __('Updated successfully'));
    }

}
