{% extends "global/base.tpl" %}
{% block title %} {{ parent() }} {{ lang('store') }}{% endblock %}
{% block meta_description %}{{ parent() }} store{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, store{% endblock meta_keywords %}
{% block page_header %}
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h2 class="headline">{{ lang('some_products_you_might_also_like') }}</h2>
			</div>
		</div>
	</div>
{% endblock page_header %}
{% block content %}
	<div class="recommend-list">
		{{ breadcrumb }}
		<div class="row">
			<div class="col-md-12">
				{% if products %}
					<div class="products featured">
						<div class="scroll">
							{% for p in products %}
								<div id="product-{{ p.product_id }}" class="row">
									<div class="col-md-12">
										<div class="card mb-3">
											<div class="card-body">
												<div class="row">
													<div class="col-md-2">
														<div class="thumbnail">
															{{ image('products', p.photo_file_name, p.product_name, 'img-fluid mx-auto img-thumbnail d-block') }}
														</div>
													</div>
													<div class="col-md-10">
														<h3 class="name">
															<a href="{{ page_url('product', p) }}">
																{{ p.product_name }}
															</a>
														</h3>
														{% if p.avg_ratings %}
														<div class="star-rating">{{ format_ratings(p.avg_ratings)}}</div>
														{% endif %}
														<p class="overview">{{ p.product_overview }}</p>
														<p class="price">{{ product_price(p) }}</p>
														<p class="text-right">
															{% if p.product_type == 'subscription' %}
																<a href="{{ page_url('product', p) }}"
																   class="btn btn-warning subscription">{{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }}
																	{{  lang('payment_options') }}</a>
															{% else %}
																{% if login_for_price(p) == false %}
																	<a href="{{ site_url }}cart/add/{{ p.product_id }}"
																	   class="btn btn-primary buy-now">
																		{{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang('add_to_'~layout_design_shopping_cart_or_bag) }}</a>
																{% endif %}
															{% endif %}
														</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							{% endfor %}
						</div>
					</div>
				{% endif %}
				<div class="row">
					<div class="col-md-12 text-sm-right">
						<a href="{{ site_url('cart') }}" class="submit-button btn btn-lg btn-info">
							{{ i('fa fa-caret-right') }} <span>{{ lang('no_thanks') }}, {{ lang('proceed_to_cart') }}</span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
{% endblock content %}
{% block javascript_footer %}
	{{ parent() }}
	<script>
        $('.submit-button').on('click', function () {
            $('.submit-button i').removeClass('fa-caret-right');
            $('.submit-button i').addClass('fa-refresh fa-spin');
            $('.submit-button').addClass('disabled');
            $('.submit-button span').html('{{ lang('please_wait') }}');
        });
	</script>
{% endblock javascript_footer %}
