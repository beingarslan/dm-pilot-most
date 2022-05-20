<?php

$passed        = true;
$results       = [];
$minPhpVersion = '7.2.5';
$extensions    = [
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

// Extension checker
$results['extensions'] = [];
foreach ($extensions as $extension) {
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
    'required'  => $minPhpVersion,
    'success'   => version_compare(PHP_VERSION, $minPhpVersion) >= 0 ? true : false,
];

// Pass check
foreach ($results['extensions'] as $extension) {
    if ($extension['success'] == false) {
        $passed = false;
        break;
    }
}

if ($results['php']['success'] == false) {
    $passed = false;
}

?>

<?php if ($passed) {?>
    <h3 style="text-align: center; color: green;">DM PILOT CAN BE INSTALLED</h3>
<?php } else {?>
    <h3 style="text-align: center; color: red;">DM PILOT CAN'T BE INSTALLED</h3>
<?php }?>

<table cellpadding="5" cellspacing="0" border="1" width="500" align="center">
    <thead>
        <tr>
            <th>PHP version installed</th>
            <th>PHP version required</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align="center">
                <?=$results['php']['installed'];?>
            </td>
            <td align="center">
                <?=$results['php']['required'];?>
            </td>
            <td align="center">
                <?php if ($results['php']['success']) {?>
                    <strong style="color: green;">PASSED</strong>
                <?php } else {?>
                    <strong style="color: red;">NOT PASSED</strong>
                <?php }?>
            </td>
        </tr>
    </tbody>
</table>

<br>

<table cellpadding="5" cellspacing="0" border="1" width="500" align="center">
    <thead>
        <tr>
            <th>PHP Extension</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results['extensions'] as $extension) {?>
        <tr>
            <td align="center">
                <?=$extension['extension'];?>
            </td>
            <td align="center">
                <?php if ($extension['success']) {?>
                    <strong style="color: green;">INSTALLED</strong>
                <?php } else {?>
                    <strong style="color: red;">NOT INSTALLED</strong>
                <?php }?>
            </td>
        </tr>
        <?php }?>
    </tbody>
</table>

