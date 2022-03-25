<?php
$schema = '{% schema %}
	{
		"name": "Footer",
		"blocks": [
		  {
			"type": "social",
			"name": "Social accounts",
			"limit": 1,
			"settings": [
			  {
				"type": "checkbox",
				"id": "title_enable",
				"label": "Show header",
				"default": true
			  },
			  {
				"type": "text",
				"id": "title",
				"label": "Heading",
				"default": "Follow us"
			  },
			  {
				"type": "radio",
				"id": "align",
				"label": "Icon alignment",
				"options": [
				  { "value": "left", "label": "Left" },
				  { "value": "center", "label": "Center" },
				  { "value": "right", "label": "Right" }
				],
				"default": "left"
			  }
			]
		  },
		  {
			"type": "menu",
			"name": "Menu",
			"settings": [
			  {
				"id": "link_list",
				"type": "link_list",
				"label": "Menu",
				"default": "footer"
			  }
			]
		  },
		  {
			"type": "text",
			"name": "Text",
			"settings": [
			  {
				"type": "text",
				"id": "title",
				"label": "Heading",
				"default": "Text column"
			  },
			  {
				"type": "richtext",
				"id": "text",
				"label": "Text",
				"default": "<p>Share store details, promotions, or brand content with your customers</p>"
			  }
			]
		  },
		  {
			"type": "newsletter",
			"name": "Newsletter",
			"limit": 1,
			"settings": [
			  {
				"type": "color",
				"id": "color_newsletter_input",
				"label": "Newsletter field",
				"default": "#1a1a1a"
			  },
			  {
				"type": "color",
				"id": "color_newsletter_input_text",
				"label": "Newsletter text",
				"default": "#fff"
			  },
			  {
				"type": "text",
				"id": "title",
				"label": "Heading"
			  },
			  {
				"type": "textarea",
				"id": "text",
				"label": "Text"
			  }
			]
		  }
		]
	}
{% endschema %}';