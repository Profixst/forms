{% set form = __SELF__.form %}
{% set recaptcha = __SELF__.recaptcha %}

<div class="form-{{ form.code }}">
    <p>{{ form.description|raw }}</p>

    <form
        id="form-{{ form.code }}"
        data-request="onSubmitForm"
        data-request-success="$(this).remove()"
        data-request-update="'{{ __SELF__ }}::result': '.callback-form-result'"
        data-request-files
        enctype="multipart/form-data"
    >
        <input type="hidden" name="form_id" value="{{ form.id }}">

        {% for field in form.fields %}
            {% if field.code is not empty %}

                {% if field.type == 'text' %}
                    <div class="form-group field-{{ field.code }}">
                        <label for="{{ field.code }}">{{ field.title }}</label>
                        <input class="form-control" type="text" name="{{ field.code }}" id="{{ field.code }}" placeholder="{{ field.placeholder }}" required>
                    </div>

                {% elseif field.type == 'textarea' %}
                    <div class="form-group field-{{ field.code }}">
                        <label for="{{ field.code }}">{{ field.title }}</label>
                        <textarea class="form-control" name="{{ field.code }}" id="{{ field.code }}" placeholder="{{ field.placeholder }}" rows="7" required></textarea>
                    </div>

                {% elseif field.type == 'select' %}
                    <div class="form-group field-{{ field.code }}">
                        <label for="{{ field.code }}">{{ field.title }}</label>
                        <select class="form-control" name="{{ field.code }}" id="{{ field.code }}" required>
                            <option value="">{{ field.placeholder }}</option>
                            {% for option in field.options %}
                                <option value="{{ option.value }}">{{ option.title }}</option>
                            {% endfor %}
                        </select>
                    </div>

                {% elseif field.type == 'recaptcha' %}
                    {% if recaptcha and recaptcha.site_key is not empty %}
                        <input type="hidden" name="g-recaptcha-response" value="">
                        <script src="https://www.google.com/recaptcha/api.js?render={{ recaptcha.site_key }}"></script>
                        <script>
                            (function () {
                                const formId = 'form-{{ form.code }}';
                                grecaptcha.ready(function () {
                                    grecaptcha.execute('{{ recaptcha.site_key }}', {action: '{{ recaptcha.action|default('submit') }}'}).then(function (token) {
                                        const form = document.getElementById(formId);
                                        if (form) {
                                            let input = form.querySelector('[name="g-recaptcha-response"]');
                                            if (!input) {
                                                input = document.createElement('input');
                                                input.type = 'hidden';
                                                input.name = 'g-recaptcha-response';
                                                form.appendChild(input);
                                            }
                                            input.value = token;
                                        }
                                    });
                                });
                            })();
                        </script>
                    {% else %}
                        <div class="alert alert-danger">⚠️ reCAPTCHA sitekey не передано. Віджет не працюватиме.</div>
                    {% endif %}

                {% elseif field.type == 'radio' %}
                    <div class="form-group field-radio">
                        <label>{{ field.title }}</label>
                        {% for option in field.options %}
                            <label class="radio-inline">
                                <input type="radio" name="{{ field.code }}" value="{{ option.value }}" id="{{ form.code ~ '_' ~ field.code ~ '_' ~ loop.index }}">
                                {{ option.title }}
                            </label>
                        {% endfor %}
                    </div>

                {% elseif field.type == 'checkbox' %}
                    <div class="form-group form-check field-{{ field.code }}">
                        <input class="form-check-input" type="checkbox" name="{{ field.code }}" id="{{ form.code ~ '_' ~ field.code ~ '_1' }}">
                        <label class="form-check-label" for="{{ form.code ~ '_' ~ field.code ~ '_1' }}">{{ field.title }}</label>
                    </div>

                {% elseif field.type == 'multicheckbox' %}
                    <div class="form-group form-check field-{{ field.code }}">
                        <label>{{ field.title }}</label>
                        {% for option in field.options %}
                            <label class="radio-inline">
                                <input type="checkbox" name="{{ field.code }}[]" value="{{ option.value }}" id="{{ form.code ~ '_' ~ field.code ~ '_' ~ loop.index }}">
                                {{ option.title }}
                            </label>
                        {% endfor %}
                    </div>

                {% elseif field.type == 'label' %}
                    <div class="form-group">
                        <label>{{ field.title }}</label>
                    </div>

                {% elseif field.type == 'file' %}
                    <div class="form-group form-check field-{{ field.code }}">
                        <label for="{{ form.code ~ '_' ~ field.code ~ '_file' }}">{{ field.title }}</label>
                        <input class="form-check-input" type="file" name="{{ field.code }}" id="{{ form.code ~ '_' ~ field.code ~ '_file' }}">
                    </div>

                {% endif %}
            {% endif %}
        {% endfor %}

        <button type="submit" class="btn btn-primary">{{ form.submit_text }}</button>
    </form>

    <div class="callback-form-result"></div>
</div>
