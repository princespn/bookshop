<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 *  This is a sample MySQL update file for JROX that will run SQL updates on the database schema... `
 *  You can use both Query Builder or Normal SQL statements for certain updates.  Use update_vars
 * for Query Builder
 and update_sql for straight SQL.
 *
 * update_sql overrides update_vars if both are included in update
 *
 */
/*
$updates[] = array( //FOR INSERTING DATA USING QUERY BUILDER  FOR SETTINGS.
                    'update_type' => 'CONFIG',
                    'config_key'  => 'TESTING',
                    'update_vars' => array('settings_key'        => 'TESTING1',
                                           'settings_value'      => 'TEST',
                                           'settings_module'     => 'settings',
                                           'settings_type'       => 'test',
                                           'settings_group'      => '11',
                                           'settings_sort_order' => '0',
                                           'settings_function'   => 'none',
                    ),
                    //'update_sql'  => "INSERT INTO `jrox_settings`(`settings_key`, `settings_value`, `settings_module`, `settings_type`, `settings_group`, `settings_sort_order`, `settings_function`)VALUES ('TESTING2', 'TEST', 'settings', 'text', '11', '0', 'none')",
);


$updates[] = array( //FOR NORMAL INSERTS

                    'update_type' => 'INSERT',

                    'check_sql' => 'SELECT * FROM ' . $this->db->dbprefix('email_templates') . ' WHERE template_name = \'test_template\'',

                    'update_sql' => "INSERT INTO `jrox_email_templates`
						(`template_id`, `html`, `email_type`, `status`, `template_name`, `from_name`, `from_email`, `cc`, `bcc`, `description`)
					VALUES (NULL, '1', 'affiliate', '1', 'test_template', '{{site_name}}', '{{site_email}}', '', '', 'Sends an email to the dowline of the user');",

                    'supporting_sql' => "INSERT INTO `jrox_email_templates_name` (`template_name_id`, `template_id`, `language_id`, `subject`, `text_body`, `html_body`)
					VALUES (NULL, '{{mysql_insert_id}}', '{{language_id}}', 'TEST', 'TEST', 'TEST');",
                    'set_lang' => TRUE
);

$updates[] = array(
	'update_type' => 'COLUMN',
	'table_name'  => 'jrox_videos',
	'column_name' => 'screenshot',

	'update_sql' => "ALTER TABLE `jrox_videos`  ADD `test` VARCHAR(255) NOT NULL DEFAULT '';",
);

$updates[] = array(
	'update_type' => 'GENERAL',

	'update_sql' => "ALTER TABLE `jrox_cart_items` CHANGE `discount_amount` `discount_amount` DECIMAL(15,3) NOT NULL DEFAULT '0.00';",
);

$updates[] = array(
	'update_type' => 'WIDGET',

	'update_data' => array('widget_name'     => 'test',
	                       'widget_category' => '1',
	                       'description'     => 'test widget',
	                       'image'           => '',
	                       'preview_code'    => '<div class="container" data-type="preview" data-id="{{widget_id}}"> <img src="//www.domain.com/images/widgets/blogs-placeholder.jpg" data-fixed="1" />  </div>',
	                       'template_code'   => '{% if latest_blog_posts %}
    <div class="widget" data-type="widget" data-function="latest_blog_posts">
        <div id="latest-blog-posts" class="container section">
            <div class="title">
                <div class="inner">
                    <h2 class="text-xs-center"><span>{{ lang(\'latest_blog_posts\') }}</span></h2>
                </div>
            </div>
            <div class="carousel-three">
                {% for p in latest_blog_posts %}
                    <div class="item">
                        <div class="card">
                            <figure class="gallery-item">
                                {% if p.overview_image %}
                                    <img src="{{ p.overview_image }}" class="img-fluid"/>
                                {% else %}
                                    <img src="{{ base_url }}images/no-photo.jpg" class="img-fluid"/>
                                {% endif %}
                                <figcaption class="hover-box">
                                    <h5><a href="{{ page_url(\'blog\', p) }}"
                                           class="btn btn-primary btn-sm item-details">{{ lang(\'read_more\') }}</a></h5>
                                </figcaption>
                            </figure>
                            <div class="card-body">
                                <h5><a href="{{ page_url(\'blog\', p) }}">{{ p.title }}</a></h5>
                                <p>{{ p.overview }}</p>
                                <div>
                                    <small>{{ display_date(p.date_published) }}</small>
                                    <span class="float-right">                    
                                        {% if sts_content_disqus_shortname %}
                                        <a href="{{ page_url(\'blog\', p) }}#disqus_thread"
                                           class="label label-default"></a>
                                        {% else %}
                                        {% if p.comments %}
                                            <a href="{{ page_url(\'blog\', p) }}#comments"
                                               class="label label-default">{{ i(\'fa fa-comments-o\') }} {{ p.comments }}</a>
                                        {% endif %}                  
                                    </span>
                                    {% endif %}
                                </div>
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
{% endif %}',
	                       'meta_data'       => '',
	                       'footer_data'     => ''),

);
*/



/* End of file sql_update.php */
/* Location: ./application/updates/sql/sql_update.php */



