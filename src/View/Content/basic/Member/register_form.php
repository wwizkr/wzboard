<h1>Login</h1>
<?php if (isset($viewData['error'])): ?>
	<p style="color:red;"><?php echo $viewData['error']; ?></p>
<?php endif; ?>
<form action="/auth/login" method="POST">
	<label for="email">Email:</label>
	<input type="text" id="email" name="email" required>
	<br>
	<label for="password">Password:</label>
	<input type="password" id="password" name="password" required>
	<br>
	<button type="submit">Login</button>
</form>
