<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require_once "config/Database.php";
  require_once "models/User.php";

  $database = new Database();
  $db = $database->getConnection();
  $user = new User($db);

  $user->email = $_POST['email'] ?? '';
  $user->username = $_POST['username'] ?? '';
  $user->password = $_POST['password'] ?? '';
  $user->confirm_password = $_POST['confirm_password'] ?? '';

  // Debugging
  if (empty($user->email) || empty($user->username) || empty($user->password)) {
      echo "Error: Missing input fields.";
      exit();
  }

  if ($user->create()) {
      header("Location: login.php");
      exit();
  } else {
      $errors[] = "Unable to create user. Please check your input.";
  }
}

?>

<?php include('views/register-head.php') ?>
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
      Create account
    </h1>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Email</span>
        <input
          name="email"
          type="email"
          required
          class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
          placeholder="Jane Doe@gmail.com" />
      </label>

      <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Username</span>
        <input
          name="username"
          type="text"
          required
          class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
          placeholder="Jane Doe" />
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

      <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">
          Confirm password
        </span>
        <input
          name="confirm_password"
          type="password"
          required
          class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
          placeholder="***************" />
      </label>

      <div class="flex mt-6 text-sm">
        <label class="flex items-center dark:text-gray-400">
          <input
            type="checkbox"
            class="text-purple-600 form-checkbox focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray" />
        </label>
      </div>

      <button
        type="submit"
        class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
        Create account
      </button>
    </form>

    <hr class="my-8" />

    <p class="mt-4">
      <a
        class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline"
        href="login.php">
        Already have an account? Login
      </a>
    </p>
    
  </div>
</div>

<?php include('views/register-footer.php') ?>