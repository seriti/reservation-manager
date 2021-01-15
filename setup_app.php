<?php
/*
NB: This is not stand alone code and is intended to be used within "seriti/slim3-skeleton" framework
The code snippet below is for use within an existing src/setup_app.php file within this framework
add the below code snippet to the end of existing "src/setup_app.php" file.
This tells the framework about module: name, sub-memnu route list and title, database table prefix.
*/

$container['config']->set('module','reserve',['name'=>'Reservation manager',
                                             'route_root'=>'admin/reserve/',
                                             'route_list'=>['dashboard'=>'Dashboard','reserve'=>'Reservations','package'=>'Packages',
                                                            'calendar'=>'Calendar','setup_dashboard'=>'Setup'],
                                             'labels'=>['item'=>'Unit','package'=>'Package','package_category'=>'Category'],
                                             'images'=>['access'=>'PRIVATE','width'=>900,'height'=>600,'width_tn'=>120,'height_tn'=>80],
                                             'table_prefix'=>'res_'
                                             ]);