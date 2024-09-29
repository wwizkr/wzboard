<?php
foreach ($this->getAssets('js') as $jsFile) {
    echo '<script src="' . htmlspecialchars($jsFile, ENT_QUOTES, 'UTF-8') . '?'.time().'"></script>' . PHP_EOL;
}
?>
</body>
</html>