<?php

use Lib\Prisma\Classes\Prisma;
use Lib\StateManager;
use Lib\Auth\Auth;

$prisma = new Prisma();
$store = new StateManager();
$auth = new Auth();

if ($auth->isAuthenticated()) {
    redirect('/dashboard');
}

$message = '';
$messageType = false;
$name = '';
$email = '';
$password = '';

if ($isPost) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $store->setState($_POST);

    if (empty($name) || empty($email) || empty($password)) {
        $store->setState(['message' => "All fields are required", 'messageType' => false]);
    } else {
        $user = $prisma->user->findUnique([
            'where' => [
                'email' => $email,
            ]
        ]);

        if ($user) {
            $store->setState([
                'message' => "User already exists",
                'messageType' => false
            ], true);
        } else {
            $prisma->user->create([
                'data' => [
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'userRole' => [
                        'connectOrCreate' => [
                            'where' => [
                                'name' => 'User'
                            ],
                            'create' => [
                                'name' => 'User'
                            ]
                        ]
                    ]
                ]
            ]);
            $store->setState([
                'message' => "User created successfully",
                'messageType' => true
            ], true);
        }

        redirect('/auth/register');
    }
}

if ($store->getState('message')) {
    $message = $store->getState('message');
    $messageType = $store->getState('messageType');
    if ($messageType) {
        $name = '';
        $email = '';
        $password = '';
    } else {
        $name = $store->getState('name');
        $email = $store->getState('email');
        $password = $store->getState('password');
    }
}

$store->resetState(true);

?>


<div class="w-screen h-screen grid place-items-center">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm w-full max-w-md" data-v0-t="card">
        <div class="p-0">
            <div class="flex flex-col gap-4 p-6">
                <h3 class="text-2xl font-semibold whitespace-nowrap leading-none tracking-tight text-center">
                    <h2 class="text-lg font-bold">Create an account</h2>
                </h3>
                <p class="text-center text-xl <?= $messageType ? 'text-green-600' : 'text-red-500' ?>"><?= $message ?></p>
                <form class="space-y-2" method="POST">
                    <div class="flex flex-col gap-2"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="name">Name</label>
                        <input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="name" placeholder="Enter your name" required type="text" name="name" value="<?= $name ?? '' ?>">
                    </div>

                    <div class="flex flex-col gap-2"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="email">Email</label>
                        <input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="email" placeholder="Enter your email" required type="email" name="email" value="<?= $email ?? '' ?>">
                    </div>

                    <div class="flex flex-col gap-2"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="password">Password</label>
                        <input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="password" placeholder="Enter your password" required type="password" name="password" value="<?= $password ?? '' ?>">
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400">Already have an account? <a href="/auth/login" class="text-blue-500">Log in</a>.</p>
                    <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-blue-500 text-white hover:bg-primary/90 h-10 px-4 py-2 w-full" type="submit">
                        Create an account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>