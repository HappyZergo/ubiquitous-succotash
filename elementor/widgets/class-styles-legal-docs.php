<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Elementor_Styles_Legal_Docs_Widget extends Widget_Base
{
    /**
     * @inheritDoc
     */
    public function get_name(): string
    {
        return 'pigeonpixel-styles-legal-docs';
    }

    /**
     * @inheritDoc
     */
    public function get_title(): string
    {
        return __('Styles Legal Docs', 'pigeonpixel');
    }

    /**
     * @inheritDoc
     */
    public function get_icon(): string
    {
        return 'eicon-document-file';
    }

    /**
     * @inheritDoc
     */
    public function get_categories(): array
    {
        return [ 'custom-widgets' ];
    }

    /**
     * @inheritDoc
     */
    public function get_keywords(): array
    {
        return [
            'style',
            'legal',
            'docs'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function register_controls()
    {
        /**
         * Content tab
         */
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Content', 'pigeonpixel'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('content', [
            'label'    => __('Custom HTML', 'pigeonpixel'),
            'type'     => Controls_Manager::CODE,
            'language' => 'html',
            'rows'     => 20,
        ]);

        $this->end_controls_section();

        /**
         * Style tab
         */
        $this->start_controls_section('style_section', [
            'label' => esc_html__('Style', 'pigeonpixel'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $tags = [
            'p',
            'a',
            'h1',
            'h2',
            'h3',
            'h4'
        ];

        foreach ( $tags as $tag ) {
            $this->add_control('heading_' . $tag, [
                'label'     => __('Style for ' . $tag, 'pigeonpixel'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]);

            $this->add_control('color_' . $tag, [
                'label'     => esc_html__('Color', 'pigeonpixel'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . ( $tag === 'p' ? '.wpembed-content' : $tag ) => 'color: {{VALUE}}'
                ],
            ]);

            $this->add_group_control(Group_Control_Typography::get_type(), [
                'name'     => 'typography_' . $tag,
                'selector' => '{{WRAPPER}} ' . ( $tag === 'p' ? '.wpembed-content' : $tag ),
            ]);
        }

        $this->end_controls_section();
    }

    public function get_style_depends()
    {
        return [];
    }

    public function get_script_depends()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function render()
    {
        $slug     = $this->get_name();
        $settings = $this->get_settings_for_display();

        ob_start(); ?>
        <div class="inner-content-style">
            <?php echo $settings['content']; ?>
        </div>
        <?php
        echo ob_get_clean();
    }
}
