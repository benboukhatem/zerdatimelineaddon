<?php 
/**
* Zerda Timeline ..
*
* @package Composant Dev Zerda - Timeline
* @copyright Copyright (C) 2023 Zerda Group - support@zerda.digital
* @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
*
* @elementor-plugin
* Plugin Name: Zerda Timeline
* Version: 1.2
* Plugin URI: https://zerda.digital/
* Description: Internal component specific to the Zerda group, An Elementor widget allowing to create a Timeline.
* Author: Team developer Zerda
* Author URI: https://zerda.digital/
* Text Domain: elementor-zerda-Timeline
* License: GPL v3
* Requires at least: 6.0
* Requires PHP: 5.6.20
*/
defined( 'ABSPATH' ) || exit; 

class zerdTimeLineAddon{

    public function __construct()
    {
        add_action('elementor/widgets/register',array($this,'registerTimeLineZerda'));
        add_action("wp_enqueue_scripts", array($this,"zerdaTimeCss"));//Css
        add_action("wp_enqueue_scripts",array($this,"zerdaTimeJs"));//Js
         // AJAX
         add_action( 'wp_footer', array($this,'time_ajax_zerda') );        
         add_action("wp_ajax_zerdaTimeLineAjax" , array($this,"zerdaTimeLineAjax"));
         add_action("wp_ajax_nopriv_zerdaTimeLineAjax" , array($this,"zerdaTimeLineAjax"));
    }
    public function registerTimeLineZerda($widgetsManager){
        require_once ('widgets/zerdatimelinewidget.php');
        $widgetsManager->register(new \zerdaTimeLineWedgets());
    }

    public function time_ajax_zerda() { ?> 
		 <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <script type="text/javascript" >
			
        jQuery(document).ready(function($) {
			 $(".nextTime, .prevTime").click(function(){
				 var getIdTime =  $( '.swiper-slide-active' ).attr("data-idtime");
				
				 $(this).attr('data-idtime', getIdTime);
			 });
            ajaxurlT = '<?php echo admin_url( 'admin-ajax.php' ) ?>'; 
            $(".clicYearTime, .nextTime, .prevTime").click(function(){
				
				$( '.zer-horizontal-timeline .loadingline' ).removeClass( "selected" );
				 
				 $( '.zer-horizontal-timeline .loadingline .ui-widget-header' ).fadeOut();
                var dataIdTime = $(this).attr("data-idtime");
                var divYearTime = $(this).attr("id");
				if(window.matchMedia("(max-width: 480px)").matches){
					$(".swiper-slide-active .loadingline" ).addClass( "selected" );
				}else{
					$("#"+divYearTime+" .loadingline" ).addClass( "selected" );
				}
				 
				
                var dataT = {
                    'action':'zerdaTimeLineAjax',
                    'dataIdTime': ''+dataIdTime+'' 
                 };
                $.ajax({
                    url: ajaxurlT,
                    type: 'POST',
                    data: dataT,
                    beforeSend: function () { 
                        $("#"+divYearTime+" .loadingline").progressbar({
						  value: false,
						  change: function() {
							  //$( '.zer-horizontal-timeline #'+divYearTime+' .loadingline .ui-widget-header' ).fadeIn();
						  },
						  complete: function() {
							  
							  //$( '.zer-horizontal-timeline #'+divYearTime+' .loadingline .ui-widget-header' ).css( "background", "#1AB89F" );
						  }
						});
                    },
                    complete: function () {
						
                        $("#"+divYearTime+" .loadingline").progressbar({
						  value: 100 
						});
						setTimeout(function() { 
							$(this).addClass( "loadinglineItem" );
       						$( '.zer-horizontal-timeline #'+divYearTime+' .loadingline .ui-widget-header' ).css( "background", "#1AB89F" );
							$( '.zer-horizontal-timeline .about_time').addClass('fadeContentTime'); 
  						  }, 1000);
						
                    },
                    success: function (response) {
						$('.events-content').fadeIn(800).html(response);
                    }  
                });
					
            });
         
        });
        </script> 
        <?php
    }
    public function zerdaTimeLineAjax()
    {
        if(!isset($_POST['dataIdTime'])){
            $idtimejax = 796;
        }
        else{
            $idtimejax = $_POST['dataIdTime'];
        } 
		echo json_encode($this->zerdaGetContentTime($idtimejax),'false');
    	//die();
    }

    public function zerdaGetYearTime($args){
        $arr_posts = new WP_Query( $args );
        if ($arr_posts->have_posts()):
            $i = 0;
            while ( $arr_posts->have_posts() ) :
                $arr_posts->the_post();
                if ($i == 0) {
                    $clSele = "selected";
                } else {
                    $clSele = "";}
                ?>  
					<div id="year-<?php echo get_the_ID(); ?>" data-idtime="<?php echo get_the_ID(); ?>" class="clicYearTime swiper-slide"><div class="yearAffiche"><?php echo esc_attr(get_post_meta(get_the_ID(), 'timeline_year', true)); ?></div><div class="loadingline  <?php echo $clSele; ?>"></div></div>
                    
                <?php
                $i++;
            endwhile;
        endif;
    }
    public function zerdaGetContentTime($idtimeline){
        $args = array(
            'p'         => $idtimeline,
            'post_type' => 'time-line',
            'post_status' => 'publish',
            'category_name' => 'Timeline',
            'order' => 'ASC',
        ); 
        $arr_posts = new WP_Query( $args );

        if ($arr_posts->have_posts()):
           
            while ( $arr_posts->have_posts() ) :
                $arr_posts->the_post();
                ?>  
                <div class="about_time">
                    <?php $time_img_url = get_the_post_thumbnail_url( $arr_posts->ID,'full'); ?>
					<div class="time-picture" style="background-image: url('<?php echo $time_img_url;?>');" ></div>
                    <div class="time-content">
                        <h2 class="title-time-element"><?php the_title(); ?></h2>
                        <div class="content-time-element">
                            <?php the_excerpt(); ?>
                            <!--<a href="<?php the_permalink(); ?>">Lien</a>-->
                        </div>
                    </div>                  
                </div>
                <?php
            endwhile;
        endif;
    }
    public function zerdaCreateTime(){
        $args = array(
            'post_type' => 'time-line',
            'post_status' => 'publish',
            'category_name' => 'Timeline',
            'order' => 'ASC',
        );        
        ?>	
	   <div class="zer-horizontal-timeline">
		   <h2 class="timeline-title">Canderel Full Timeline</h2>
	   <div class="swiper timeline">
		   <div class="swiper-wrapper">
			   <?php echo $this->zerdaGetYearTime($args);	?>
		   </div>
		   
		</div>
		   <div class="nextTime"></div>
		   <div class="prevTime"></div>
		<script> 
		var swiper = new Swiper(".timeline", {
			slidesPerView: 9,
			spaceBetween: 50,			
			breakpoints: {
				320: {
				  slidesPerView: 1,
				  spaceBetween: 1
				},	
				480: {
				  slidesPerView: 1,
				  spaceBetween: 1
				},
				640: {
				  slidesPerView: 9,
				  spaceBetween: 50
				}
      		 },
			freeMode: true,
			navigation: {
			nextEl: ".nextTime",
			prevEl: ".prevTime",
		  },
		});
		</script>
       
		   <!-- .timeline -->
		   <div class="events-content" id="events-content">
				   <?php echo $this->zerdaGetContentTime(796);?>  
		   </div>
		   <!-- .events-content -->
    </div>
        <?php
    }
    // Add Css page
    public function zerdaTimeCss(){
        wp_register_style("zerdatimecss",plugins_url("zerdatimelineaddon/css/zerdatime.css"));
        wp_enqueue_style("zerdatimecss");
    }
    public function zerdaTimeJs(){
        wp_register_script("zerdatimeJs",plugins_url("zerdatimelineaddon/js/zerdatime.js"));
        wp_enqueue_script("zerdatimeJs");
    }


}
new zerdTimeLineAddon();

?>
