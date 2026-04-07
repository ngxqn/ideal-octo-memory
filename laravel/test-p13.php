<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\User::unguard();
$admin = App\Models\User::firstOrCreate(['email' => 'a13@a.com'], ['username' => 'a13', 'role' => 'admin', 'password' => '1', 'full_name' => '1', 'phone' => '1', 'address' => '1', 'commune' => '1','city' => '1']);
$customer = App\Models\User::firstOrCreate(['email' => 'c13@c.com'], ['username' => 'c13', 'role' => 'customer', 'password' => '1', 'full_name' => '1', 'phone' => '1', 'address' => '1', 'commune' => '1','city' => '1']);

// Define routes dynamically
$router = $app->make('router');
$router->get('/test-admin', function() { return 'admin ok'; })->middleware('role:admin');
$router->get('/test-user', function() { return 'user ok'; })->middleware('role:customer');

// Helper to simulate request
function checkRoute($app, $uri, $user = null) {
    echo "Testing $uri " . ($user ? "with user: {$user->role}" : "as guest") . " ... ";
    $request = Illuminate\Http\Request::create($uri, 'GET');
    if ($user) $request->setUserResolver(function() use ($user) { return $user; });
    
    try {
        $httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $response = $httpKernel->handle($request);
        echo "Status: " . $response->getStatusCode() . "\n";
    } catch (\Exception $e) {
        if ($e instanceof \Illuminate\Auth\AuthenticationException || $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            echo "Caught: " . get_class($e) . " - " . $e->getMessage() . "\n";
        } else {
            echo "Unexpected Exception: " . get_class($e) . " - " . $e->getMessage() . "\n";
        }
    }
}

checkRoute($app, '/test-admin'); // Guest
checkRoute($app, '/test-admin', $customer); // Customer on admin route
checkRoute($app, '/test-admin', $admin); // Admin on admin route
checkRoute($app, '/test-user', $customer); // Customer on user route
