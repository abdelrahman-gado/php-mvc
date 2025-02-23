<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>{{ title }}</title>
</head>

<body></body>
<h1>Products</h1>
<p>Total: {{ total }}</p>
<a href="/products/new">New Product</a>
    {% foreach ($products as $product): %}
        <h2><a href="/products/{{ product["id"] }}/show">{{ product["name"] }}</a></a></h2>
        <p>{{ product["description"] }}</p>
    {% endforeach; %}
</body>

</html>