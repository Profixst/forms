{% if __SELF__.form.is_auth_required %}

	{% if __SELF__.user %}
		<p>
			<span>{{ __SELF__.user.email }}</span>
			<button onclick="$.ajax({url: '/logout', method: 'POST'})">Logout</button>
		</p>

		{% partial __SELF__ ~ '::form' %}
	{% else %}
		<a href="{{ __SELF__.authUrl }}">Login</a>
	{% endif %}

{% else %}
	{% partial __SELF__ ~ '::form' %}
{% endif %}