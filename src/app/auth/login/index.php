<?php

use Lib\Prisma\Classes\Prisma;
use Lib\Validator;
use Lib\Auth\Auth;
use Lib\Auth\AuthRole;

$prisma = new Prisma();
$auth = new Auth();

if ($auth->isAuthenticated()) {
    if (AuthRole::Admin->equals($user['userRole'])) {
        redirect('/dashboard');
    } else {
        redirect('/dashboard');
    }
}

$message = '';
$messageType = false;

if ($isPost) {
    $email = Validator::validateString($_POST['email'] ?? '');
    $password = Validator::validateString($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $message = "Email and password are required";
    } else {

        $user = $prisma->user->findUnique([
            'where' => [
                'email' => $email,
            ],
            'select' => [
                'id' => true,
                'name' => true,
                'email' => true,
                'password' => true,
                'userRole' => [
                    'select' => [
                        'name' => true,
                    ],
                ],
            ],
        ]);

        if (!$user) {
            $message = "Invalid email or password";
        } else {
            if (!isset($user['password']) || !password_verify($password, $user['password'])) {
                $message = "Invalid email or password";
            } else {
                $user = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'userRole' => $user['userRole'][0]['name'],
                ];
                $auth->authenticate($user, '1m');
                if (AuthRole::Admin->equals($user['userRole'])) {
                    redirect('/dashboard/users');
                } else {
                    redirect('/dashboard');
                }
            }
        }
    }
}

?>

<div class="flex items-center justify-center h-screen bg-gray-100 dark:bg-gray-900">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <h1 class="mb-6 text-2xl font-bold text-center text-gray-800 dark:text-white">Welcome Back</h1>
        <p class="text-center mb-4 dark:text-gray-400 text-xl <?= $messageType ? 'text-green-600' : 'text-red-500' ?>"><?= $message ?></p>
        <form class="space-y-4" method="POST">
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                    Email
                </label>
                <input id="email" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Enter your email" type="email" name="email" value="<?= $email ?? '' ?>" />
            </div>
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                    Password
                </label>
                <input id="password" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white dark:border-gray-600" placeholder="Enter your password" type="password" name="password" value="<?= $password ?? '' ?>" />
            </div>
            <div class="flex justify-between">
                <button type="submit" class="w-full px-4 py-2 font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Login
                </button>
            </div>
            <div class="text-center space-y-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">Don't have an account? <a href="/auth/register" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-500">Sign up</a></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Forgot your password? <a href="/auth/forgot-password" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-500">Reset password</a></p>
            </div>
    </div>
</div>