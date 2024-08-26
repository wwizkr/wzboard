<!-- components/skinname/menu.php -->
<ul>
    <?php foreach ($menuData as $menuItem): ?>
        <li><?= htmlspecialchars($menuItem['me_name']) ?></li>
    <?php endforeach; ?>
</ul>