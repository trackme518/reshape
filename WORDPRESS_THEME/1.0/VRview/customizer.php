<?php

function vrview_options($wp_customize) {
//create new section in customizer:-------------------
  $wp_customize->add_section('vrview', array(
      'title'      => __('3D visualization','vrview'),
      'priority'   => 30,
  ) ) ;
//create new setttings-----------------------------
  $wp_customize->add_setting(
    'classifier',
    array(
        'default' => 'categories',
    )
  );
  
    $wp_customize->add_setting(
    'spread',
    array(
        'default' => '10',
    )
  );
  
    $wp_customize->add_setting(
    'random_offset',
    array(
        'default' => '2',
    )
  );
  
    $wp_customize->add_setting(
    'iterations',
    array(
        'default' => '1000',
    )
  );
  
  $wp_customize->add_setting(
    'camdistance',
    array(
        'default' => '15',
    )
  );
  
    $wp_customize->add_setting(
    'learnrate',
    array(
        'default' => '10',
    )
  );
  
    $wp_customize->add_setting(
    'perplexity',
    array(
        'default' => '5',
    )
  );
  
  $wp_customize->add_setting(
    'color-label',
    array(
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    )
);

  $wp_customize->add_setting(
    'color-background',
    array(
        'default' => '#FFFFFF',
        'sanitize_callback' => 'sanitize_hex_color',
    )
);
  

//add controls --------------------------------

  //let the user choose what to use for classification
  $wp_customize->add_control(
          'classifier',
          array(
              'label'          => 'What to use for classification:',
              'section'        => 'vrview',
              'type'           => 'radio',
              'choices'        => array(
                  'tags'   => __( 'Tags' ),
                  'categories'  => __( 'Categories' )
              )
          )
  );

 
  //let the user control how much spread there should be between elements
  $wp_customize->add_control(
          //$wp_customize,
          'spread',
          array(
              'label'          => __( 'How far apart are the items from each other:', 'vrview' ),
              'section'        => 'vrview',
              'type'           => 'number',
              'default'        => '10',
          )
  );
  
  //let the user control random offset to prevent overlap
  $wp_customize->add_control(
         // $wp_customize,
          'random_offset',
          array(
              'label'          => __( 'Random offset:', 'vrview' ),
              'section'        => 'vrview',
              'type'           => 'number',
              'default'        => '2',
          )
  );
  
//let the user control random offset to prevent overlap
  $wp_customize->add_control(
         // $wp_customize,
          'iterations',
          array(
              'label'          => __( 'tSNE iterations:', 'vrview' ),
              'section'        => 'vrview',
              'type'           => 'number',
              'default'        => '1000',
          )
  );
 //set initial camera position 
$wp_customize->add_control(
         // $wp_customize,
          'camdistance',
          array(
              'label'          => __( 'Initial camera distance:', 'vrview' ),
              'section'        => 'vrview',
              'type'           => 'number',
              'default'        => '15',
          )
  );
  
   //set learning rate 
$wp_customize->add_control(
         // $wp_customize,
          'learnrate',
          array(
              'label'          => __( 'Learning rate of tSNE:', 'vrview' ),
              'section'        => 'vrview',
              'type'           => 'number',
              'default'        => '10',
          )
  );
  
     //set perplexity - number of nearist neigbour to care about
$wp_customize->add_control(
         // $wp_customize,
          'perplexity',
          array(
              'label'          => __( 'Perplexity of tSNE (nearest neighbor count):', 'vrview' ),
              'section'        => 'vrview',
              'type'           => 'number',
              'default'        => '5',
          )
  );
  
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
        $wp_customize,
        'color-label',
        array(
            'label' => 'Color of the labels:',
            'section' => 'vrview',
        )
    )
);

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
        $wp_customize,
        'color-background',
        array(
            'label' => 'Background color:',
            'section' => 'vrview',
        )
    )
);


}

//make it happen--------------------------------
add_action('customize_register', 'vrview_options');

?>