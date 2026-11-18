<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class YesNo_Checker_Widget extends Widget_Base {

    public function get_name() { return 'yesno_checker'; }
    public function get_title() { return 'Yes/No Checker'; }
    public function get_icon() { return 'eicon-checkbox'; }
    public function get_categories() { return [ 'general' ]; }

    protected function register_controls() {

        // ── LAYOUT ───────────────────────────────────────────────────
        $this->start_controls_section( 'section_layout', [
            'label' => 'Layout',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control( 'widget_layout', [
            'label'   => 'Layout Style',
            'type'    => Controls_Manager::SELECT,
            'default' => 'normal',
            'options' => [ 'normal' => 'Normal', 'tabbed' => 'Tabbed' ],
        ]);
        $this->end_controls_section();

        // ── HEADER ───────────────────────────────────────────────────
        $this->start_controls_section( 'section_header', [
            'label' => 'Header',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control( 'widget_title', [
            'label'   => 'Title',
            'type'    => Controls_Manager::TEXT,
            'default' => 'Check your eligibility',
            'dynamic' => [ 'active' => true ],
        ]);
        $this->add_control( 'widget_description', [
            'label'   => 'Description',
            'type'    => Controls_Manager::WYSIWYG,
            'default' => 'Answer the questions below to see your result.',
            'dynamic' => [ 'active' => true ],
        ]);
        $this->end_controls_section();

        // ── NORMAL: Questions ─────────────────────────────────────────
        $this->start_controls_section( 'section_questions', [
            'label'     => 'Questions',
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [ 'widget_layout' => 'normal' ],
        ]);
        $q_rep = new Repeater();
        $q_rep->add_control( 'question_text', [
            'label'       => 'Question',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Your question here?',
            'label_block' => true,
            'dynamic'     => [ 'active' => true ],
        ]);
        $this->add_control( 'questions', [
            'label'       => 'Questions',
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $q_rep->get_controls(),
            'default'     => [
                [ 'question_text' => 'Was your reading under 0.15?' ],
                [ 'question_text' => 'Do you hold an open licence?' ],
                [ 'question_text' => 'No suspensions in the last 5 years?' ],
            ],
            'title_field' => '{{{ question_text }}}',
        ]);
        $this->end_controls_section();

        // ── NORMAL: Result Conditions ─────────────────────────────────
        $this->start_controls_section( 'section_results', [
            'label'     => 'Result Conditions',
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [ 'widget_layout' => 'normal' ],
        ]);

        $this->add_control( 'result_conditions_note', [
            'type' => Controls_Manager::RAW_HTML,
            'raw'  => '<div style="background:#f0f4ff;border-left:3px solid #4a90e2;padding:10px 12px;border-radius:4px;font-size:12px;line-height:1.6;color:#333;">
                Rules checked <strong>top to bottom</strong> — first match wins.<br>
                <strong>Operators:</strong> == &nbsp;!= &nbsp;&gt; &nbsp;&gt;= &nbsp;&lt; &nbsp;&lt;=<br>
                <strong>Value:</strong> number <em>or</em> <code>all</code> (= total questions)
            </div>',
        ]);

        $r_rep = new Repeater();

        $r_rep->add_control( 'rule_subject', [
            'label'   => 'Check',
            'type'    => Controls_Manager::SELECT,
            'default' => 'yes',
            'options' => [
                'yes'        => '— Yes count',
                'no'         => '— No count',
                'question_1' => 'Q1 answer',
                'question_2' => 'Q2 answer',
                'question_3' => 'Q3 answer',
                'question_4' => 'Q4 answer',
                'question_5' => 'Q5 answer',
                'question_6' => 'Q6 answer',
                'question_7' => 'Q7 answer',
                'question_8' => 'Q8 answer',
                'question_9' => 'Q9 answer',
                'question_10'=> 'Q10 answer',
            ],
        ]);

        $r_rep->add_control( 'rule_operator', [
            'label'   => 'Operator',
            'type'    => Controls_Manager::SELECT,
            'default' => '==',
            'options' => [
                '=='     => '== (equal)',
                '!='     => '!= (not equal)',
                '>'      => '>  (greater than)',
                '>='     => '>= (greater or equal)',
                '<'      => '<  (less than)',
                '<='     => '<= (less or equal)',
                'is_yes' => 'is Yes',
                'is_no'  => 'is No',
                'is_eq'  => 'is equal to →',
            ],
        ]);

        $r_rep->add_control( 'rule_value', [
            'label'       => 'Value',
            'type'        => Controls_Manager::TEXT,
            'default'     => '3',
            'placeholder' => 'Number or "all"',
            'description' => 'Use a number (e.g. 2) or "all" for total questions.',
            'label_block' => false,
            'condition'   => [
                'rule_subject' => [ 'yes', 'no' ],
            ],
        ]);

        $r_rep->add_control( 'rule_q_value', [
            'label'     => 'Answer is',
            'type'      => Controls_Manager::SELECT,
            'default'   => 'yes',
            'options'   => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'condition' => [
                'rule_subject'  => [ 'question_1','question_2','question_3','question_4','question_5',
                                     'question_6','question_7','question_8','question_9','question_10' ],
                'rule_operator' => 'is_eq',
            ],
        ]);

        $r_rep->add_control( 'rule_message', [
            'label'       => 'Message',
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => 'Enter your result message here.',
            'label_block' => true,
            'dynamic'     => [ 'active' => true ],
        ]);

        $r_rep->add_control( 'rule_style', [
            'label'   => 'Message Style',
            'type'    => Controls_Manager::SELECT,
            'default' => 'auto',
            'options' => [
                'auto'    => 'Auto (based on yes/no ratio)',
                'success' => 'Green (success)',
                'warning' => 'Yellow (warning)',
                'danger'  => 'Red (danger)',
                'neutral' => 'Grey (neutral)',
            ],
        ]);

        $this->add_control( 'result_rules', [
            'label'       => 'Result Rules',
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $r_rep->get_controls(),
            'default'     => [
                [
                    'rule_subject'  => 'yes',
                    'rule_operator' => '==',
                    'rule_value'    => 'all',
                    'rule_message'  => 'You look fully eligible. Contact us before your court date.',
                    'rule_style'    => 'success',
                ],
                [
                    'rule_subject'  => 'yes',
                    'rule_operator' => '>=',
                    'rule_value'    => '2',
                    'rule_message'  => 'You may partially qualify. Call us to discuss your options.',
                    'rule_style'    => 'warning',
                ],
                [
                    'rule_subject'  => 'yes',
                    'rule_operator' => '<',
                    'rule_value'    => '2',
                    'rule_message'  => 'You do not appear to qualify. Call us to discuss edge cases.',
                    'rule_style'    => 'danger',
                ],
            ],
            'title_field' => '{{{ rule_subject }}} {{{ rule_operator }}} {{{ rule_value }}}',
        ]);

        $this->add_control( 'footer_text', [
            'label'       => 'Footer Text',
            'type'        => Controls_Manager::WYSIWYG,
            'default'     => '',
            'placeholder' => 'Optional footer note — supports links, bold, etc.',
            'separator'   => 'before',
            'dynamic'     => [ 'active' => true ],
        ]);

        $this->end_controls_section();

        // ── TABBED: Tabs ──────────────────────────────────────────────
        $this->start_controls_section( 'section_tabs', [
            'label'     => 'Tabs',
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [ 'widget_layout' => 'tabbed' ],
        ]);

        $tab_rep = new Repeater();

        // ── Tab card info ─────────────────────────
        $tab_rep->add_control( 'tab_label', [
            'label'       => 'Tab Title',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Tab',
            'label_block' => true,
            'dynamic'     => [ 'active' => true ],
        ]);
        $tab_rep->add_control( 'tab_subtitle', [
            'label'       => 'Tab Subtitle',
            'type'        => Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => 'Optional subtitle',
            'label_block' => true,
            'dynamic'     => [ 'active' => true ],
        ]);
        $tab_rep->add_control( 'tab_description', [
            'label'       => 'Tab Description',
            'type'        => Controls_Manager::WYSIWYG,
            'default'     => '',
            'placeholder' => 'Optional description shown below the tab button title/subtitle.',
            'dynamic'     => [ 'active' => true ],
        ]);
        $tab_rep->add_control( 'tab_section_label', [
            'label'       => 'Section Label',
            'type'        => Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => 'e.g. WORK LICENCE — RESTRICTED LICENCE UNDER S 87',
            'label_block' => true,
            'description' => 'Uppercase label shown above the questions inside this tab panel.',
            'dynamic'     => [ 'active' => true ],
        ]);

        // ── Questions repeater inside each tab ────
        $tab_rep->add_control( 'tab_items_heading', [
            'label'     => 'Questions',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $inner_q = new Repeater();
        $inner_q->add_control( 'item_text', [
            'label'       => 'Question',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Your question here?',
            'label_block' => true,
            'dynamic'     => [ 'active' => true ],
        ]);

        $tab_rep->add_control( 'tab_questions', [
            'label'       => 'Questions',
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $inner_q->get_controls(),
            'default'     => [
                [ 'item_text' => 'Your question here?' ],
            ],
            'title_field' => '{{{ item_text }}}',
        ]);

        // ── Result rules same as Normal layout ────
        $tab_rep->add_control( 'tab_rules_heading', [
            'label'     => 'Result Conditions',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $tab_rep->add_control( 'tab_rules_note', [
            'type' => Controls_Manager::RAW_HTML,
            'raw'  => '<div style="background:#f0f4ff;border-left:3px solid #4a90e2;padding:8px 10px;border-radius:4px;font-size:11px;line-height:1.5;color:#333;">Rules checked top → bottom. First match wins.<br><strong>Value:</strong> number or <code>all</code></div>',
        ]);

        $rule_inner = new Repeater();
        $rule_inner->add_control( 'rule_subject', [
            'label'   => 'Check',
            'type'    => Controls_Manager::SELECT,
            'default' => 'yes',
            'options' => [
                'yes'         => '— Yes count',
                'no'          => '— No count',
                'question_1'  => 'Q1 answer',
                'question_2'  => 'Q2 answer',
                'question_3'  => 'Q3 answer',
                'question_4'  => 'Q4 answer',
                'question_5'  => 'Q5 answer',
                'question_6'  => 'Q6 answer',
                'question_7'  => 'Q7 answer',
                'question_8'  => 'Q8 answer',
                'question_9'  => 'Q9 answer',
                'question_10' => 'Q10 answer',
            ],
        ]);
        $rule_inner->add_control( 'rule_operator', [
            'label'   => 'Operator',
            'type'    => Controls_Manager::SELECT,
            'default' => '==',
            'options' => [
                '=='     => '== equal',
                '!='     => '!= not equal',
                '>'      => '>  greater than',
                '>='     => '>= greater or equal',
                '<'      => '<  less than',
                '<='     => '<= less or equal',
                'is_yes' => 'is Yes',
                'is_no'  => 'is No',
                'is_eq'  => 'is equal to →',
            ],
        ]);
        $rule_inner->add_control( 'rule_value', [
            'label'       => 'Value',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'all',
            'placeholder' => 'Number or "all"',
            'condition'   => [
                'rule_subject' => [ 'yes', 'no' ],
            ],
        ]);
        $rule_inner->add_control( 'rule_q_value', [
            'label'     => 'Answer is',
            'type'      => Controls_Manager::SELECT,
            'default'   => 'yes',
            'options'   => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'condition' => [
                'rule_subject'  => [ 'question_1','question_2','question_3','question_4','question_5',
                                     'question_6','question_7','question_8','question_9','question_10' ],
                'rule_operator' => 'is_eq',
            ],
        ]);
        $rule_inner->add_control( 'rule_message', [
            'label'       => 'Message',
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => 'Enter your result message.',
            'label_block' => true,
            'dynamic'     => [ 'active' => true ],
        ]);
        $rule_inner->add_control( 'rule_style', [
            'label'   => 'Style',
            'type'    => Controls_Manager::SELECT,
            'default' => 'auto',
            'options' => [
                'auto'    => 'Auto',
                'success' => 'Green',
                'warning' => 'Yellow',
                'danger'  => 'Red',
                'neutral' => 'Grey',
            ],
        ]);

        $tab_rep->add_control( 'tab_rules', [
            'label'       => 'Result Rules',
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $rule_inner->get_controls(),
            'default'     => [
                [ 'rule_subject' => 'yes', 'rule_operator' => '==', 'rule_value' => 'all',  'rule_message' => 'You look fully eligible!',          'rule_style' => 'success' ],
                [ 'rule_subject' => 'yes', 'rule_operator' => '>=', 'rule_value' => '1',    'rule_message' => 'You may partially qualify.',         'rule_style' => 'warning' ],
                [ 'rule_subject' => 'yes', 'rule_operator' => '==', 'rule_value' => '0',    'rule_message' => 'You do not appear to qualify.',       'rule_style' => 'danger'  ],
            ],
            'title_field' => '{{{ rule_subject }}} {{{ rule_operator }}} {{{ rule_value }}}',
        ]);

        $tab_rep->add_control( 'tab_footer_text', [
            'label'       => 'Tab Footer Text',
            'type'        => Controls_Manager::WYSIWYG,
            'default'     => '',
            'placeholder' => 'Optional footer note for this tab — supports links, bold, etc.',
            'separator'   => 'before',
            'dynamic'     => [ 'active' => true ],
        ]);

        $this->add_control( 'tabs', [
            'label'       => 'Tabs',
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $tab_rep->get_controls(),
            'default'     => [
                [
                    'tab_label'    => 'Option A',
                    'tab_subtitle' => 'Description for option A',
                ],
                [
                    'tab_label'    => 'Option B',
                    'tab_subtitle' => 'Description for option B',
                ],
            ],
            'title_field' => '{{{ tab_label }}}',
        ]);
        $this->end_controls_section();

        // ── TABBED: Pre-selection Text ────────────────────────────────
        $this->start_controls_section( 'section_preselect', [
            'label'     => 'Pre-selection Text',
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [ 'widget_layout' => 'tabbed' ],
        ]);
        $this->add_control( 'preselect_text', [
            'label'       => 'Text (shown before any tab is chosen)',
            'type'        => Controls_Manager::WYSIWYG,
            'default'     => '',
            'placeholder' => 'e.g. Please select an option above to begin.',
            'dynamic'     => [ 'active' => true ],
        ]);
        $this->end_controls_section();

        // ── STYLE: Card ───────────────────────────────────────────────
        $this->start_controls_section( 'section_style_card', [
            'label' => 'Card',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control( 'card_bg_color', [
            'label'     => 'Background Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .ync-card' => 'background-color: {{VALUE}};' ],
        ]);
        $this->add_control( 'card_border_radius', [
            'label'     => 'Border Radius',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'   => [ 'size' => 16 ],
            'selectors' => [ '{{WRAPPER}} .ync-card' => 'border-radius: {{SIZE}}px;' ],
        ]);
        $this->add_control( 'card_padding', [
            'label'      => 'Padding',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => '32', 'right' => '36', 'bottom' => '32', 'left' => '36', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->add_control( 'card_box_shadow_heading', [
            'label'     => 'Box Shadow',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'card_shadow_toggle', [
            'label'        => 'Enable Shadow',
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
        ]);
        $this->add_control( 'card_shadow_color', [
            'label'     => 'Shadow Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(0,0,0,0.08)',
            'condition' => [ 'card_shadow_toggle' => 'yes' ],
            'selectors' => [ '{{WRAPPER}} .ync-card' => 'box-shadow: 0 2px 20px {{VALUE}};' ],
        ]);
        $this->end_controls_section();

        // ── STYLE: Title ──────────────────────────────────────────────
        $this->start_controls_section( 'section_style_title', [
            'label' => 'Title',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control( 'title_color', [
            'label'     => 'Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1a1a1a',
            'selectors' => [ '{{WRAPPER}} .ync-title' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .ync-title',
        ]);
        $this->add_control( 'title_margin', [
            'label'      => 'Margin',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '0', 'right' => '0', 'bottom' => '20', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();

        // ── STYLE: Description ────────────────────────────────────────
        $this->start_controls_section( 'section_style_desc', [
            'label' => 'Description',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control( 'desc_color', [
            'label'     => 'Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#555555',
            'selectors' => [ '{{WRAPPER}} .ync-desc' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'desc_typography',
            'selector' => '{{WRAPPER}} .ync-desc',
        ]);
        $this->add_control( 'desc_margin', [
            'label'      => 'Margin',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '0', 'right' => '0', 'bottom' => '20', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();

        // ── STYLE: Question Rows ──────────────────────────────────────
        $this->start_controls_section( 'section_style_rows', [
            'label' => 'Question Rows',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control( 'row_bg_color', [
            'label'     => 'Row Background',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5f3ee',
            'selectors' => [ '{{WRAPPER}} .ync-question-row' => 'background-color: {{VALUE}};' ],
        ]);
        $this->add_control( 'row_border_color', [
            'label'     => 'Row Border Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ece9e2',
            'selectors' => [ '{{WRAPPER}} .ync-question-row' => 'border-color: {{VALUE}};' ],
        ]);
        $this->add_control( 'row_border_radius', [
            'label'     => 'Row Border Radius',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'   => [ 'size' => 10 ],
            'selectors' => [ '{{WRAPPER}} .ync-question-row' => 'border-radius: {{SIZE}}px;' ],
        ]);
        $this->add_control( 'row_padding', [
            'label'      => 'Row Padding',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '14', 'right' => '20', 'bottom' => '14', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-question-row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->add_control( 'row_gap', [
            'label'     => 'Gap Between Rows',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'   => [ 'size' => 10 ],
            'selectors' => [ '{{WRAPPER}} .ync-questions' => 'gap: {{SIZE}}px;' ],
        ]);
        $this->add_control( 'question_text_heading', [
            'label'     => 'Question Text',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'question_text_color', [
            'label'     => 'Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1a1a1a',
            'selectors' => [ '{{WRAPPER}} .ync-question-text' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'question_typography',
            'selector' => '{{WRAPPER}} .ync-question-text',
        ]);
        $this->end_controls_section();

        // ── STYLE: Yes/No Buttons ─────────────────────────────────────
        $this->start_controls_section( 'section_style_buttons', [
            'label' => 'Yes/No Buttons',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control( 'btn_inactive_bg', [
            'label'     => 'Default Background',
            'type'      => Controls_Manager::COLOR,
            'default'   => 'transparent',
            'selectors' => [ '{{WRAPPER}} .ync-btn' => 'background-color: {{VALUE}};' ],
        ]);
        $this->add_control( 'btn_inactive_border', [
            'label'     => 'Default Border Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#c8c5be',
            'selectors' => [ '{{WRAPPER}} .ync-btn' => 'border-color: {{VALUE}};' ],
        ]);
        $this->add_control( 'btn_inactive_text', [
            'label'     => 'Default Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#444444',
            'selectors' => [ '{{WRAPPER}} .ync-btn' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'btn_active_heading', [
            'label'     => 'Active State',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'btn_yes_active_color', [
            'label'   => 'Yes — Active Background',
            'type'    => Controls_Manager::COLOR,
            'default' => '#2d6a4f',
        ]);
        $this->add_control( 'btn_no_active_color', [
            'label'   => 'No — Active Background',
            'type'    => Controls_Manager::COLOR,
            'default' => '#a63228',
        ]);
        $this->add_control( 'btn_text_active', [
            'label'   => 'Active Text Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#ffffff',
        ]);
        $this->add_control( 'btn_size_heading', [
            'label'     => 'Size & Shape',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'btn_border_radius', [
            'label'     => 'Border Radius',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'   => [ 'size' => 50 ],
            'selectors' => [ '{{WRAPPER}} .ync-btn' => 'border-radius: {{SIZE}}px;' ],
        ]);
        $this->add_control( 'btn_padding', [
            'label'      => 'Padding',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '6', 'right' => '20', 'bottom' => '6', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'btn_typography',
            'selector' => '{{WRAPPER}} .ync-btn',
        ]);
        $this->add_control( 'btn_hover_heading', [
            'label'     => 'Hover State',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'btn_hover_border', [
            'label'   => 'Hover Border Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#999999',
        ]);
        $this->add_control( 'btn_hover_text', [
            'label'   => 'Hover Text Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#111111',
        ]);
        $this->add_control( 'btn_hover_bg', [
            'label'   => 'Hover Background',
            'type'    => Controls_Manager::COLOR,
            'default' => 'transparent',
        ]);
        $this->end_controls_section();

        // ── STYLE: Result Box ─────────────────────────────────────────
        $this->start_controls_section( 'section_style_result', [
            'label' => 'Result Box',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control( 'result_border_radius', [
            'label'     => 'Border Radius',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'   => [ 'size' => 10 ],
            'selectors' => [ '{{WRAPPER}} .ync-result' => 'border-radius: {{SIZE}}px;' ],
        ]);
        $this->add_control( 'result_padding', [
            'label'      => 'Padding',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '14', 'right' => '18', 'bottom' => '14', 'left' => '18', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-result' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->add_control( 'result_margin_top', [
            'label'     => 'Margin Top',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'default'   => [ 'size' => 12 ],
            'selectors' => [ '{{WRAPPER}} .ync-result' => 'margin-top: {{SIZE}}px;' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'result_typography',
            'selector' => '{{WRAPPER}} .ync-result-text',
        ]);

        // ── Success (all Yes) ──
        $this->add_control( 'result_success_heading', [
            'label'     => 'Success — All Yes',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'result_success_bg', [
            'label'   => 'Background',
            'type'    => Controls_Manager::COLOR,
            'default' => '#e8f5ee',
        ]);
        $this->add_control( 'result_success_border', [
            'label'   => 'Border Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#8ec9a6',
        ]);
        $this->add_control( 'result_success_text', [
            'label'   => 'Text Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#1a5c35',
        ]);

        // ── Warning (mixed) ──
        $this->add_control( 'result_warning_heading', [
            'label'     => 'Warning — Mixed',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'result_warning_bg', [
            'label'   => 'Background',
            'type'    => Controls_Manager::COLOR,
            'default' => '#fef9e6',
        ]);
        $this->add_control( 'result_warning_border', [
            'label'   => 'Border Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#e2c85a',
        ]);
        $this->add_control( 'result_warning_text', [
            'label'   => 'Text Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#7a5800',
        ]);

        // ── Danger (all No) ──
        $this->add_control( 'result_danger_heading', [
            'label'     => 'Danger — All No',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'result_danger_bg', [
            'label'   => 'Background',
            'type'    => Controls_Manager::COLOR,
            'default' => '#fdecea',
        ]);
        $this->add_control( 'result_danger_border', [
            'label'   => 'Border Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#e8a09a',
        ]);
        $this->add_control( 'result_danger_text', [
            'label'   => 'Text Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#8b2015',
        ]);

        // ── Neutral ──
        $this->add_control( 'result_neutral_heading', [
            'label'     => 'Neutral',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'result_neutral_bg', [
            'label'   => 'Background',
            'type'    => Controls_Manager::COLOR,
            'default' => '#f4f4f4',
        ]);
        $this->add_control( 'result_neutral_border', [
            'label'   => 'Border Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#cccccc',
        ]);
        $this->add_control( 'result_neutral_text', [
            'label'   => 'Text Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#444444',
        ]);

        $this->end_controls_section();

        // ── STYLE: Footer Text ────────────────────────────────────────
        $this->start_controls_section( 'section_style_footer', [
            'label' => 'Footer Text',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control( 'footer_color', [
            'label'     => 'Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#777777',
            'selectors' => [ '{{WRAPPER}} .ync-footer, {{WRAPPER}} .ync-footer a' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'footer_link_color', [
            'label'     => 'Link Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2d6a4f',
            'selectors' => [ '{{WRAPPER}} .ync-footer a' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'footer_typography',
            'selector' => '{{WRAPPER}} .ync-footer',
        ]);
        $this->add_control( 'footer_margin', [
            'label'      => 'Margin',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '16', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-footer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();

        // ── STYLE: Tab Buttons ────────────────────────────────────────
        $this->start_controls_section( 'section_style_tabs', [
            'label'     => 'Tab Buttons',
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'widget_layout' => 'tabbed' ],
        ]);
        $this->add_control( 'tab_inactive_bg', [
            'label'     => 'Default Background',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f2f0eb',
            'selectors' => [ '{{WRAPPER}} .ync-tab-btn' => 'background-color: {{VALUE}};' ],
        ]);
        $this->add_control( 'tab_inactive_text', [
            'label'     => 'Default Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#333333',
            'selectors' => [ '{{WRAPPER}} .ync-tab-btn' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'tab_active_heading', [
            'label'     => 'Active State',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'tab_active_bg', [
            'label'     => 'Active Background',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1e4d35',
            'selectors' => [ '{{WRAPPER}} .ync-tab-btn.ync-tab-active' => 'background-color: {{VALUE}}; border-color: {{VALUE}};' ],
        ]);
        $this->add_control( 'tab_active_text', [
            'label'     => 'Active Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .ync-tab-btn.ync-tab-active' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'tab_size_heading', [
            'label'     => 'Size & Shape',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'tab_border_radius', [
            'label'     => 'Border Radius',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'   => [ 'size' => 12 ],
            'selectors' => [ '{{WRAPPER}} .ync-tab-btn' => 'border-radius: {{SIZE}}px;' ],
        ]);
        $this->add_control( 'tab_padding', [
            'label'      => 'Padding',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '16', 'right' => '18', 'bottom' => '16', 'left' => '18', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-tab-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->add_control( 'tab_gap', [
            'label'     => 'Gap Between Tabs',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'   => [ 'size' => 10 ],
            'selectors' => [ '{{WRAPPER}} .ync-tab-nav' => 'gap: {{SIZE}}px;' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'tab_title_typography',
            'label'    => 'Title Typography',
            'selector' => '{{WRAPPER}} .ync-tab-title',
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'tab_subtitle_typography',
            'label'    => 'Subtitle Typography',
            'selector' => '{{WRAPPER}} .ync-tab-subtitle',
        ]);
        $this->add_control( 'tab_hover_heading', [
            'label'     => 'Hover State (inactive tabs)',
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control( 'tab_hover_bg', [
            'label'   => 'Hover Background',
            'type'    => Controls_Manager::COLOR,
            'default' => '#e8e6e0',
        ]);
        $this->add_control( 'tab_hover_border', [
            'label'   => 'Hover Border Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '#cccccc',
        ]);
        $this->add_control( 'tab_hover_text', [
            'label'   => 'Hover Text Color',
            'type'    => Controls_Manager::COLOR,
            'default' => '',
        ]);
        $this->end_controls_section();

        // ── STYLE: Tab Description ────────────────────────────────────
        $this->start_controls_section( 'section_style_tab_desc', [
            'label'     => 'Tab Description',
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'widget_layout' => 'tabbed' ],
        ]);
        $this->add_control( 'tab_desc_color', [
            'label'     => 'Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#444444',
            'selectors' => [ '{{WRAPPER}} .ync-tab-desc' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'tab_desc_link_color', [
            'label'     => 'Link Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2d6a4f',
            'selectors' => [ '{{WRAPPER}} .ync-tab-desc a' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'tab_desc_link_hover', [
            'label'     => 'Link Hover Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1a5c35',
            'selectors' => [ '{{WRAPPER}} .ync-tab-desc a:hover' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'tab_desc_typography',
            'selector' => '{{WRAPPER}} .ync-tab-desc',
        ]);
        $this->add_control( 'tab_desc_margin', [
            'label'      => 'Margin',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '16', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-tab-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();

        // ── STYLE: Section Label ──────────────────────────────────────
        $this->start_controls_section( 'section_style_section_label', [
            'label'     => 'Section Label',
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'widget_layout' => 'tabbed' ],
        ]);
        $this->add_control( 'section_label_color', [
            'label'     => 'Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2d6a4f',
            'selectors' => [ '{{WRAPPER}} .ync-section-label' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'section_label_bg', [
            'label'     => 'Background',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#f5faf7',
            'selectors' => [ '{{WRAPPER}} .ync-section-label' => 'background-color: {{VALUE}};' ],
        ]);
        $this->add_control( 'section_label_border_color', [
            'label'     => 'Border Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#c8dfd0',
            'selectors' => [ '{{WRAPPER}} .ync-section-label' => 'border-color: {{VALUE}};' ],
        ]);
        $this->add_control( 'section_label_border_radius', [
            'label'     => 'Border Radius',
            'type'      => Controls_Manager::SLIDER,
            'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'   => [ 'size' => 10 ],
            'selectors' => [ '{{WRAPPER}} .ync-section-label' => 'border-radius: {{SIZE}}px;' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'section_label_typography',
            'selector' => '{{WRAPPER}} .ync-section-label',
        ]);
        $this->add_control( 'section_label_padding', [
            'label'      => 'Padding',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '14', 'right' => '18', 'bottom' => '14', 'left' => '18', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-section-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->add_control( 'section_label_margin', [
            'label'      => 'Margin',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '0', 'right' => '0', 'bottom' => '12', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-section-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();

        // ── STYLE: Pre-selection Text ─────────────────────────────────
        $this->start_controls_section( 'section_style_preselect', [
            'label'     => 'Pre-selection Text',
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'widget_layout' => 'tabbed' ],
        ]);
        $this->add_control( 'preselect_color', [
            'label'     => 'Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#666666',
            'selectors' => [ '{{WRAPPER}} .ync-preselect-text' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'preselect_link_color', [
            'label'     => 'Link Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2d6a4f',
            'selectors' => [ '{{WRAPPER}} .ync-preselect-text a' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'preselect_link_hover', [
            'label'     => 'Link Hover Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1a5c35',
            'selectors' => [ '{{WRAPPER}} .ync-preselect-text a:hover' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'preselect_typography',
            'selector' => '{{WRAPPER}} .ync-preselect-text',
        ]);
        $this->add_control( 'preselect_margin', [
            'label'      => 'Margin',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '16', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-preselect-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();

        // ── STYLE: Tab Footer ─────────────────────────────────────────
        $this->start_controls_section( 'section_style_tab_footer', [
            'label'     => 'Tab Footer Text',
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [ 'widget_layout' => 'tabbed' ],
        ]);
        $this->add_control( 'tab_footer_color', [
            'label'     => 'Text Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#777777',
            'selectors' => [ '{{WRAPPER}} .ync-tab-footer' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'tab_footer_link_color', [
            'label'     => 'Link Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#2d6a4f',
            'selectors' => [ '{{WRAPPER}} .ync-tab-footer a' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'tab_footer_link_hover', [
            'label'     => 'Link Hover Color',
            'type'      => Controls_Manager::COLOR,
            'default'   => '#1a5c35',
            'selectors' => [ '{{WRAPPER}} .ync-tab-footer a:hover' => 'color: {{VALUE}};' ],
        ]);
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'tab_footer_typography',
            'selector' => '{{WRAPPER}} .ync-tab-footer',
        ]);
        $this->add_control( 'tab_footer_margin', [
            'label'      => 'Margin',
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '16', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [ '{{WRAPPER}} .ync-tab-footer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();
    }

    // ─────────────────────────────────────────────────────────────────
    protected function render() {
        $settings  = $this->get_settings_for_display();
        $layout    = $settings['widget_layout'];
        $widget_id = $this->get_id();

        $yes_color    = ! empty( $settings['btn_yes_active_color'] ) ? $settings['btn_yes_active_color'] : '#2d6a4f';
        $no_color     = ! empty( $settings['btn_no_active_color'] )  ? $settings['btn_no_active_color']  : '#a63228';
        $active_text  = ! empty( $settings['btn_text_active'] )      ? $settings['btn_text_active']      : '#ffffff';
        $btn_h_border = ! empty( $settings['btn_hover_border'] )     ? $settings['btn_hover_border']     : '#999999';
        $btn_h_text   = ! empty( $settings['btn_hover_text'] )       ? $settings['btn_hover_text']       : '#111111';
        $btn_h_bg     = ! empty( $settings['btn_hover_bg'] )         ? $settings['btn_hover_bg']         : 'transparent';
        $tab_h_bg     = ! empty( $settings['tab_hover_bg'] )         ? $settings['tab_hover_bg']         : '#e8e6e0';
        $tab_h_border = ! empty( $settings['tab_hover_border'] )     ? $settings['tab_hover_border']     : '#cccccc';
        $tab_h_text   = ! empty( $settings['tab_hover_text'] )       ? $settings['tab_hover_text']       : '';

        // result state colors
        $r_ok_bg  = ! empty( $settings['result_success_bg'] )     ? $settings['result_success_bg']     : '#e8f5ee';
        $r_ok_bd  = ! empty( $settings['result_success_border'] ) ? $settings['result_success_border'] : '#8ec9a6';
        $r_ok_tx  = ! empty( $settings['result_success_text'] )   ? $settings['result_success_text']   : '#1a5c35';
        $r_wn_bg  = ! empty( $settings['result_warning_bg'] )     ? $settings['result_warning_bg']     : '#fef9e6';
        $r_wn_bd  = ! empty( $settings['result_warning_border'] ) ? $settings['result_warning_border'] : '#e2c85a';
        $r_wn_tx  = ! empty( $settings['result_warning_text'] )   ? $settings['result_warning_text']   : '#7a5800';
        $r_ng_bg  = ! empty( $settings['result_danger_bg'] )      ? $settings['result_danger_bg']      : '#fdecea';
        $r_ng_bd  = ! empty( $settings['result_danger_border'] )  ? $settings['result_danger_border']  : '#e8a09a';
        $r_ng_tx  = ! empty( $settings['result_danger_text'] )    ? $settings['result_danger_text']    : '#8b2015';
        $r_nt_bg  = ! empty( $settings['result_neutral_bg'] )     ? $settings['result_neutral_bg']     : '#f4f4f4';
        $r_nt_bd  = ! empty( $settings['result_neutral_border'] ) ? $settings['result_neutral_border'] : '#cccccc';
        $r_nt_tx  = ! empty( $settings['result_neutral_text'] )   ? $settings['result_neutral_text']   : '#444444';

        $id = esc_attr( $widget_id );
        ?>
        <style>
            #ync-<?php echo $id; ?> .ync-btn.active-yes {
                background-color: <?php echo esc_attr( $yes_color ); ?>;
                color: <?php echo esc_attr( $active_text ); ?>;
                border-color: <?php echo esc_attr( $yes_color ); ?>;
            }
            #ync-<?php echo $id; ?> .ync-btn.active-no {
                background-color: <?php echo esc_attr( $no_color ); ?>;
                color: <?php echo esc_attr( $active_text ); ?>;
                border-color: <?php echo esc_attr( $no_color ); ?>;
            }
            #ync-<?php echo $id; ?> .ync-btn:hover:not(.active-yes):not(.active-no) {
                background-color: <?php echo esc_attr( $btn_h_bg ); ?>;
                border-color: <?php echo esc_attr( $btn_h_border ); ?>;
                color: <?php echo esc_attr( $btn_h_text ); ?>;
            }
            #ync-<?php echo $id; ?> .ync-tab-btn:hover:not(.ync-tab-active) {
                background-color: <?php echo esc_attr( $tab_h_bg ); ?>;
                border-color: <?php echo esc_attr( $tab_h_border ); ?>;
                <?php if ( $tab_h_text ) : ?>color: <?php echo esc_attr( $tab_h_text ); ?>;<?php endif; ?>
            }
            #ync-<?php echo $id; ?> .ync-result.result-all-yes { background-color: <?php echo esc_attr( $r_ok_bg ); ?>; border-color: <?php echo esc_attr( $r_ok_bd ); ?>; }
            #ync-<?php echo $id; ?> .ync-result.result-all-yes .ync-result-text { color: <?php echo esc_attr( $r_ok_tx ); ?>; }
            #ync-<?php echo $id; ?> .ync-result.result-mixed   { background-color: <?php echo esc_attr( $r_wn_bg ); ?>; border-color: <?php echo esc_attr( $r_wn_bd ); ?>; }
            #ync-<?php echo $id; ?> .ync-result.result-mixed   .ync-result-text { color: <?php echo esc_attr( $r_wn_tx ); ?>; }
            #ync-<?php echo $id; ?> .ync-result.result-all-no  { background-color: <?php echo esc_attr( $r_ng_bg ); ?>; border-color: <?php echo esc_attr( $r_ng_bd ); ?>; }
            #ync-<?php echo $id; ?> .ync-result.result-all-no  .ync-result-text { color: <?php echo esc_attr( $r_ng_tx ); ?>; }
            #ync-<?php echo $id; ?> .ync-result.result-neutral { background-color: <?php echo esc_attr( $r_nt_bg ); ?>; border-color: <?php echo esc_attr( $r_nt_bd ); ?>; }
            #ync-<?php echo $id; ?> .ync-result.result-neutral .ync-result-text { color: <?php echo esc_attr( $r_nt_tx ); ?>; }
        </style>

        <div class="ync-card ync-layout-<?php echo esc_attr( $layout ); ?>" id="ync-<?php echo esc_attr( $widget_id ); ?>">

            <?php if ( ! empty( $settings['widget_title'] ) ) : ?>
                <h2 class="ync-title"><?php echo wp_kses_post( $settings['widget_title'] ); ?></h2>
            <?php endif; ?>

            <?php if ( ! empty( $settings['widget_description'] ) ) : ?>
                <div class="ync-desc"><?php echo wp_kses_post( $settings['widget_description'] ); ?></div>
            <?php endif; ?>

            <?php if ( $layout === 'normal' ) : ?>
                <?php $this->render_normal( $settings ); ?>
            <?php else : ?>
                <?php $this->render_tabbed( $settings ); ?>
            <?php endif; ?>

        </div>
        <?php
    }

    // ── Normal layout ─────────────────────────────────────────────────
    private function render_normal( $settings ) {
        $questions = $settings['questions'];
        $rules     = $settings['result_rules'];

        // Build rules array for JS
        $js_rules = [];
        foreach ( $rules as $rule ) {
            $js_rules[] = [
                'subject'  => $rule['rule_subject'],
                'operator' => $rule['rule_operator'],
                'value'    => strtolower( trim( $rule['rule_value'] ) ),
                'q_value'  => ! empty( $rule['rule_q_value'] ) ? $rule['rule_q_value'] : 'yes',
                'message'  => $rule['rule_message'],
                'style'    => $rule['rule_style'],
            ];
        }
        ?>
        <div class="ync-questions">
            <?php foreach ( $questions as $index => $item ) : ?>
                <div class="ync-question-row" data-index="<?php echo esc_attr( $index ); ?>">
                    <span class="ync-question-text">
                        <?php echo esc_html( ( $index + 1 ) . '. ' . $item['question_text'] ); ?>
                    </span>
                    <div class="ync-btn-group">
                        <button class="ync-btn ync-yes" data-value="yes" aria-pressed="false">Yes</button>
                        <button class="ync-btn ync-no"  data-value="no"  aria-pressed="false">No</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="ync-result" style="display:none;">
            <p class="ync-result-text"></p>
        </div>

        <?php if ( ! empty( $settings['footer_text'] ) ) : ?>
            <div class="ync-footer"><?php echo wp_kses_post( $settings['footer_text'] ); ?></div>
        <?php endif; ?>

        <script type="application/json" class="ync-data">
        {
            "layout": "normal",
            "total": <?php echo count( $questions ); ?>,
            "rules": <?php echo wp_json_encode( $js_rules ); ?>
        }
        </script>
        <?php
    }

    // ── Tabbed layout ─────────────────────────────────────────────────
    private function render_tabbed( $settings ) {
        $tabs = $settings['tabs'];

        $tabs_data = [];
        foreach ( $tabs as $tab ) {
            $rules = [];
            if ( ! empty( $tab['tab_rules'] ) ) {
                foreach ( $tab['tab_rules'] as $rule ) {
                    $rules[] = [
                        'subject'  => $rule['rule_subject'],
                        'operator' => $rule['rule_operator'],
                        'value'    => strtolower( trim( $rule['rule_value'] ) ),
                        'q_value'  => ! empty( $rule['rule_q_value'] ) ? $rule['rule_q_value'] : 'yes',
                        'message'  => $rule['rule_message'],
                        'style'    => $rule['rule_style'],
                    ];
                }
            }
            $questions = ! empty( $tab['tab_questions'] ) ? $tab['tab_questions'] : [];
            $tabs_data[] = [
                'total' => count( $questions ),
                'rules' => $rules,
            ];
        }
        ?>
        <div class="ync-tabs-wrapper">

            <div class="ync-tab-nav" role="tablist">
                <?php foreach ( $tabs as $index => $tab ) : ?>
                    <button
                        class="ync-tab-btn"
                        data-tab="<?php echo esc_attr( $index ); ?>"
                        role="tab"
                        aria-selected="false"
                        aria-controls="ync-tab-panel-<?php echo esc_attr( $index ); ?>"
                    >
                        <span class="ync-tab-title"><?php echo esc_html( $tab['tab_label'] ); ?></span>
                        <?php if ( ! empty( $tab['tab_subtitle'] ) ) : ?>
                            <span class="ync-tab-subtitle"><?php echo esc_html( $tab['tab_subtitle'] ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $tab['tab_description'] ) ) : ?>
                            <span class="ync-tab-desc"><?php echo wp_kses_post( $tab['tab_description'] ); ?></span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php if ( ! empty( $settings['preselect_text'] ) ) : ?>
                <div class="ync-preselect-text"><?php echo wp_kses_post( $settings['preselect_text'] ); ?></div>
            <?php endif; ?>

            <div class="ync-tab-panels">
                <?php foreach ( $tabs as $index => $tab ) :
                    $questions = ! empty( $tab['tab_questions'] ) ? $tab['tab_questions'] : [];
                ?>
                    <div
                        class="ync-tab-panel"
                        id="ync-tab-panel-<?php echo esc_attr( $index ); ?>"
                        role="tabpanel"
                        aria-hidden="true"
                        style="display:none;"
                    >
                        <?php if ( ! empty( $questions ) ) : ?>
                            <?php if ( ! empty( $tab['tab_section_label'] ) ) : ?>
                                <p class="ync-section-label"><?php echo wp_kses_post( $tab['tab_section_label'] ); ?></p>
                            <?php endif; ?>
                            <div class="ync-questions">
                                <?php foreach ( $questions as $i => $q ) : ?>
                                    <div class="ync-question-row" data-index="<?php echo esc_attr( $i ); ?>">
                                        <span class="ync-question-text">
                                            <?php echo esc_html( ( $i + 1 ) . '. ' . $q['item_text'] ); ?>
                                        </span>
                                        <div class="ync-btn-group">
                                            <button class="ync-btn ync-yes" data-value="yes" aria-pressed="false">Yes</button>
                                            <button class="ync-btn ync-no"  data-value="no"  aria-pressed="false">No</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="ync-result" style="display:none;"><p class="ync-result-text"></p></div>
                            <?php if ( ! empty( $tab['tab_footer_text'] ) ) : ?>
                                <div class="ync-tab-footer"><?php echo wp_kses_post( $tab['tab_footer_text'] ); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script type="application/json" class="ync-data">
        { "layout": "tabbed", "tabs": <?php echo wp_json_encode( $tabs_data ); ?> }
        </script>
        <?php
    }
}
