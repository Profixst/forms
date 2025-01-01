<div id="on-render-form"></div>

{% put scripts %}
	<script type="text/javascript">
	  $.request('onRenderAuthForm', {
	      update: {
	        '@authform': '#on-render-form'
	      }
	  })
	</script>
{% endput %}
