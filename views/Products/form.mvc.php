<label for="name">Name</label>
<input type="text" id="name" name="name" value="{{ $product['name'] }}">
{% if (isset($errors['name'])): %}
    <p>{{ $errors['name'] }}</p>
{% endif; %}

<label for="description">Description</label>
<textarea name="description" id="description">{{ $product['description'] }}</textarea>

<button>Save</button>