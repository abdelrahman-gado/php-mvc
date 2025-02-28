{% extends "base.mvc.php" %}

{% block title %}Products{% endblock %}

{% block body %}
<h1>Products</h1>
<p>Total: {{ total }}</p>

<a href="/products/new">New Product</a>

{% foreach ($products as $product): %}
<h2><a href="/products/{{ product["id"] }}/show">{{ product["name"] }}</a></a></h2>
<p>{{ product["description"] }}</p>
{% endforeach; %}

{% endblock %}