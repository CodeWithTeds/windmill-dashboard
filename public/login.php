<?php
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require_once "config/Database.php";
  require_once "models/User.php";

  $database = new Database();
  $db = $database->getConnection();
  $user = new User($db);

  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  define('ADMIN_EMAIL', 'admin@example.com');
  define('ADMIN_PASSWORD', 'Admin123!');
  define('ADMIN_USERNAME', 'Admin');

  // Check for admin credentials first
  if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
    session_start();
    $_SESSION['user_id'] = 'admin';
    $_SESSION['username'] = ADMIN_USERNAME;
    $_SESSION['email'] = ADMIN_EMAIL;
    $_SESSION['is_admin'] = true;

    header("Location: admin-home.php");
    exit();
  }
  // If not admin, proceed with regular user login
  else if ($user->login($email, $password)) {
    session_start();
    $_SESSION['user_id'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['email'] = $user->email;
    $_SESSION['is_admin'] = false;

    header("Location: index.php");
    exit();
  } else {
    $errors[] = "Invalid email or password";
  }
}
?>

<?php include('views/login-head.php') ?>

<div class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
  <div class="w-full">
    <?php if (!empty($errors)): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <?php foreach ($errors as $error): ?>
          <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <h1 class="mb-4 text-xl font-semibold text-gray-700 dark:text-gray-200">
      Login
    </h1>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Email</span>
        <input
          name="email"
          type="email"
          required
          class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
          placeholder="jane@example.com" />
      </label>

      <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Password</span>
        <input
          name="password"
          type="password"
          required
          class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
          placeholder="***************" />
      </label>

      <button
        type="submit"
        class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
        Log in
      </button>
    </form>

    <hr class="my-8" />

    <p class="mt-1">
      <a
        class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline"
        href="register.php">
        Create account
      </a>
    </p>
  </div>
</div>

<?php include('views/login-footer.php') ?>