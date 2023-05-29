<?php 
class zerdaTimeLineWedgets extends \Elementor\widget_Base{
    public function get_name(){
        return 'zerdaTimeLine';
    }
    public function get_title(){
        return esc_html__('Zerda Timeline', 'elementor-addon');
    }
    public function get_icon(){
        return 'eicon-time-line';
    }
    public function get_categories(){
        return ['basic'];
    }
    public function get_keywords(){
        return ['zerda', 'Timeline'];
    }

    protected function register_controls(){
        $this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-currency-control' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		

		$this->end_controls_section();
    }
    protected function render(){
        $bread = new zerdTimeLineAddon();
        $bread->zerdaCreateTime();
    }
}

?>