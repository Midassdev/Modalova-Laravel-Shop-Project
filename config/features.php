<?php

return [
	'enable_blog' => env('ENABLE_BLOG', false),
	'add_noindex_to_product_pages' => env('ADD_NOINDEX_TO_PRODUCT_PAGES', false),
	'disable_indexing_of_search_pages' => env('DISABLE_INDEXING_OF_SEARCH_PAGES', false),
	'remove_brands_if_no_products_even_if_public' => env('REMOVE_BRANDS_IF_NO_PRODUCTS_EVEN_IF_PUBLIC', false),
	'obfuscate_link_to_pdp' => env('OBFUSCATE_LINK_TO_PDP', true),
];
