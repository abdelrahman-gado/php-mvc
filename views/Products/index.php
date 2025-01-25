<h1>Products</h1>
<a href="/products/new">New Product</a>
    <?php foreach ($products as $product): ?>
        <h2><a href="/products/<?= $product["id"] ?>/show"><?= $product["name"] ?></a></a></h2>
        <p><?= htmlspecialchars($product["description"]) ?></p>
    <?php endforeach; ?>
</body>

</html>