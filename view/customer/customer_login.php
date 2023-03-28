<?php include '../view/shared/header.php'; ?>
<main>

    <h2>Customer Login</h2>
    <p class="error"><?php echo $message; ?></p>

    <p>You must login before you can register a product.</p>
    <!-- display a search form -->
    <form action="." method="post" id="aligned">
        <input type="hidden" name="action" value="get_customer">
        <label>Email:</label>
        <input type="text" name="email" 
               value="<?php echo htmlspecialchars($email); ?>">
        <br>
        <label>Password:</label>
        <input type="password" name="password" 
               value="<?php echo htmlspecialchars($password); ?>">
        <br>
        <label>&nbsp;</label>
        <input type="submit" value="Login">
    </form>

</main>
<?php include '../view/shared/footer.php'; ?>