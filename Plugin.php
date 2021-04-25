<?php

/**
 * Rainiar's Toolkit for Typecho - Crazy Text
 * 
 * @package RnRCrazyText
 * @author Rainiar
 * @version 1.1.0
 * @link https://rainiar.top
 */

class RnRCrazyText_EB extends Typecho_Widget_Helper_Form_Element {
    public function input($name = NULL, array $options = NULL) {}
    protected function _value($value) {}
}

class RnRCrazyText_Plugin implements Typecho_Plugin_Interface {
    private static $pattern = array(
        '@([\s\S]{0,1})\[(p-center)\]([\s\S]*?)\[/p-center\]@',
        '@([\s\S]{0,1})\[(p-right)\]([\s\S]*?)\[/p-right\]@',
        '@([\s\S]{0,1})\[(del)\]([\s\S]*?)\[/del\]@',
        '@([\s\S]{0,1})\[(u)\]([\s\S]*?)\[/u\]@',
        '@([\s\S]{0,1})\[(i)\]([\s\S]*?)\[/i\]@',
        '@([\s\S]{0,1})\[(b)\]([\s\S]*?)\[/b\]@',
        '@([\s\S]{0,1})\[(secret)\]([\s\S]*?)\[/secret\]@',
        '@([\s\S]{0,1})\[(h-center)=([1-6])\]([\s\S]*?)\[/h-center\]@',
        '@([\s\S]{0,1})\[(h-right)=([1-6])\]([\s\S]*?)\[/h-right\]@',
        '@([\s\S]{0,1})\[(color)=([\S]+?)\]([\s\S]*?)\[/color\]@'
    );

    private static $keys = array('p-center', 'del', 'u', 'i', 'b', 'secret', 'h-center', 'h-right', 'color');

    private static $color = array(
        'white' => '#ffffff',
        'yellow' => '#ffff00',
        'red' => '#ff0000',
        'fuchsia' => '#ff00ff',
        'aqua' => '#00ffff',
        'lime' => '#00ff00',
        'blue' => '#0000ff',
        'black' => '#000000',
        'gray' => '#808080',
        'green' => '#008000',
        'maroon' => '#800000',
        'navy' => '#000080',
        'olive' => '#808000',
        'purple' => '#800080',
        'teal' => '#008000',
        'silver' => '#c0c0c0'
    );
    
    private static $switches = array();

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx_980699401 = array('RnRCrazyText_Plugin', 'contentEx');
        Typecho_Plugin::factory('Widget_Archive')->footer_1870546171 = array('RnRCrazyText_Plugin', 'footer');
    }
    
    private static function rParse($content) {
        return preg_replace_callback(
            self::$pattern,
            function($a) {
                if ($a[1] == '\\')
                    return array_key_exists($a[2], self::$keys) ? substr($a[0], 1) : $a[0];
                if (count($a) == 4)
                    switch ($a[2]) {
                        case 'p-center':
                            if (!array_key_exists('ct', self::$switches)) {
                                array_push(self::$switches, 'ct');
                                return $a[1] . '<p style="text-align:center;">' . self::rParse($a[3], self::$switches) . '</p>';
                            }
                            else 
                                return $a[0];
                        case 'p-right':
                            if (!array_key_exists('ct', self::$switches)) {
                                array_push(self::$switches, 'ct');
                                return $a[1] . '<p style="text-align:right;">' . self::rParse($a[3], self::$switches) . '</p>';
                            }
                            else 
                                return $a[0];
                        case 'del':
                        case 'u':
                        case 'i':
                        case 'b':
                            return $a[1] . '<' . $a[2] . '>' . self::rParse($a[3]) . '</' . $a[2] . '>';
                        case 'secret':
                            if (!array_key_exists('tc', self::$switches) && !array_key_exists('bc', self::$switches)) {
                                array_push(self::$switches, 'tc');
                                array_push(self::$switches, 'bc');
                                return $a[1] . '<span style="background-color:#000;color:#000" title="你知道的太多了" data-secret>' . self::rParse($a[3]) . '</span>';
                            }
                            else 
                                return $a[0];
                        default:
                            return $a[0];
                    }
                elseif (count($a) == 5)
                    switch ($a[2]) {
                        case 'h-center':
                            if (!array_key_exists('ct', self::$switches)) {
                                array_push(self::$switches, 'ct');
                                return $a[1] . '<h' . $a[3] . ' style="text-align:center;">' . self::rParse($a[4]) . '</h' . $a[3] . '>';
                            }
                            else 
                                return $a[0];
                        case 'h-right':
                            if (!array_key_exists('ct', self::$switches)) {
                                array_push(self::$switches, 'ct');
                                return $a[1] . '<h' . $a[3] . ' style="text-align:right;">' . self::rParse($a[4]) . '</h' . $a[3] . '>';
                            }
                            else 
                                return $a[0];
                        case 'color':
                            preg_match_all('@[0-9a-zA-Z#$]+@', $a[3], $matches);
                            array_unique($matches[0]);
                            foreach ($matches[0] as $key) {
                                if ($key[0] == '#' && !array_key_exists('tc', self::$switches)) {
                                    if (array_key_exists(substr($key, 1), self::$color))
                                        $style .= 'color:' . self::$color[substr($key, 1)] . ';';
                                    elseif (preg_match('@^#[0-9a-fA-F]{6}$@', substr($key, 1)))
                                        $style .= 'color:' . $key . ';';
                                    else 
                                        continue;
                                    array_push(self::$switches, 'tc');
                                }
                                else if ($key[0] == '$' && !array_key_exists('bc', self::$switches)) {
                                    if (array_key_exists(substr($key, 1), self::$color))
                                        $style .= 'background-color:' . self::$color[substr($key, 1)] . ';';
                                    elseif (preg_match('@^#[0-9a-fA-F]{6}$@', substr($key, 1)))
                                        $style .= 'background-color:' . $key . ';';
                                    else 
                                        continue;
                                    array_push(self::$switches, 'bc');
                                }
                            }
                            return $a[1] . '<span style="' . $style . '">' . self::rParse($a[4]) . '</span>';
                        default:
                            return $a[0];
                    }
                else
                    return $a[0];
            },
            $content);
    }

    public static function contentEx($content, Widget_Abstract_Contents $contents, $last) {
        $content = empty($last) ? $content : $last;
        self::$switches = array();
        return self::rParse($content);
    }
    
    public static function footer() {
        echo '<script defer>$(document).ready(function(){if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent))$("[data-secret]").click(function(){if($(this).attr("style")=="background-color:#000;color:#000")$(this).attr("style","background-color:#000;color:#fff");else $(this).attr("style","background-color:#000;color:#000");});});</script>';
    }
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form) {
?>
<div class="typecho-option">
    <h2>使用手册</h2>
    <h3>基础语法</h3>
    <p>[p-center]<u style="color:gray;">&lt;要居中的段落内容&gt;</u>[/p-center]</p>
    <p>[p-right]<u style="color:gray;">&lt;要右对齐的段落内容&gt;</u>[/p-right]</p>
    <p>[del]<u style="color:gray;">&lt;要添加删除线的内容&gt;</u>[/del]</p>
    <p>[u]<u style="color:gray;">&lt;要添加下划线的内容&gt;</u>[/u]</p>
    <p>[i]<u style="color:gray;">&lt;要使用斜体的内容&gt;</u>[/i]</p>
    <p>[b]<u style="color:gray;">&lt;要使用加粗的内容&gt;</u>[/b]</p>
    <p>[secret]<u style="color:gray;">&lt;秘密内容&gt;</u>[/secret]</p>
    <p>[h-center=<u style="color:gray;">&lt;属性&gt;</u>]<u style="color:gray;">&lt;要居中的标题内容&gt;</u>[/h-center]</p>
    <p>[h-right=<u style="color:gray;">&lt;属性&gt;</u>]<u style="color:gray;">&lt;要右对齐的标题内容&gt;</u>[/h-right]</p>
    <p>[color=<u style="color:gray;">&lt;属性&gt;</u>]<u style="color:gray;">&lt;要上色的内容&gt;</u>[/color]</p>
    <p>属性内部及前后无多余空空格</p>
    <p><b>为了防止短代码间互相干扰，请务必用空格隔开两个相邻短代码块</b></p>
    <p>如果不希望短代码生效，在该短代码前添加反斜杠即可</p>
    <h3>属性</h3>
    <p>与标题相关的两个短代码属性填的是标题级数，如[h-center=3]即对&lt;h3&gt;进行居中</p>
    <p>颜色相关属性如下：</p>
    <p>#：在井号后跟随颜色代码或者6位十六进制数，表示文字颜色</p>
    <p>$：在美元号后跟随颜色代码或者6位十六进制数，表示文字背景颜色</p>
    <h3>例子</h3>
    <p>[p-center] [del] [u] [color=#008000,$yellow]This is a weird text.[/color] [/u] [/del] [/p-center]</p>
    <p>这是绿色文字，黄色背景，居中，带有删除线和下划线的加粗字</p>
    <h3>颜色代码</h3>
    <style>.center_text {text-align:center;}.odd_col {background:#cccccc}.even_col {background-color:#dfdfdf}.odd_coll {background:#eeeeee}.even_coll {background:#efefef}</style>
    <table width="100%" border="0" cellspacing="1" cellpadding="4" align="center">
        <tr>
            <td width="25%" class="center_text odd_col">颜色</td>
            <td width="25%" class="center_text even_col">代码</td>
            <td width="25%" class="center_text odd_col">颜色</td>
            <td width="25%" class="center_text even_col">代码</td>
        </tr>
        <tr>
            <td width="25%" class="center_text odd_coll">白色 <span style="color:white;background-color:black;">###</span></td>
            <td width="25%" class="center_text even_coll">white</td>
            <td width="25%" class="center_text odd_coll">黄色 <span style="color:yellow;background-color:black;">###</span></td>
            <td width="25%" class="center_text even_coll">yellow</td>
        </tr>
        <tr>
            <td width="25%" class="center_text odd_coll">红色 <span style="color:red;">###</span></td>
            <td width="25%" class="center_text even_coll">red</td>
            <td width="25%" class="center_text odd_coll">紫红色 <span style="color:fuchsia;">###</span></td>
            <td width="25%" class="center_text even_coll">fuchsia</td>
        </tr>
        <tr>
            <td width="25%" class="center_text odd_coll">水绿色 <span style="color:aqua;background-color:black;">###</span></td>
            <td width="25%" class="center_text even_coll">aqua</td>
            <td width="25%" class="center_text odd_coll">浅绿色 <span style="color:lime;background-color:black;">###</span></td>
            <td width="25%" class="center_text even_coll">lime</td>
        </tr>
        <tr>
            <td width="25%" class="center_text odd_coll">蓝色 <span style="color:blue;">###</span></td>
            <td width="25%" class="center_text even_coll">blue</td>
            <td width="25%" class="center_text odd_coll">黑色 <span style="color:black;">###</span></td>
            <td width="25%" class="center_text even_coll">black</td>
        </tr>
        <tr>
            <td width="25%" class="center_text odd_coll">灰色 <span style="color:gray;">###</span></td>
            <td width="25%" class="center_text even_coll">gray</td>
            <td width="25%" class="center_text odd_coll">绿色 <span style="color:green;">###</span></td>
            <td width="25%" class="center_text even_coll">green</td>
        </tr>
        <tr>
            <td width="25%" class="center_text odd_coll">褐色 <span style="color:maroon;">###</span></td>
            <td width="25%" class="center_text even_coll">maroon</td>
            <td width="25%" class="center_text odd_coll">深蓝色 <span style="color:navy;">###</span></td>
            <td width="25%" class="center_text even_coll">navy</td>
        </tr>
        <tr>
            <td width="25%" class="center_text odd_coll">橄榄色 <span style="color:olive;">###</span></td>
            <td width="25%" class="center_text even_coll">olive</td>
            <td width="25%" class="center_text odd_coll">紫色 <span style="color:purple;">###</span></td>
            <td width="25%" class="center_text even_coll">purple</td>
        </tr>
        <tr>
            <td width="25%" class="center_text odd_coll">深青色 <span style="color:teal;">###</span></td>
            <td width="25%" class="center_text even_coll">teal</td>
            <td width="25%" class="center_text odd_coll">银色 <span style="color:silver;">###</span></td>
            <td width="25%" class="center_text even_coll">silver</td>
        </tr>
    </table>
</div>
<?php
        $ept = new RnRCrazyText_EB('a', NULL, NULL, _t(''), _t(''));
        $form->addInput($ept);
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate() {}
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}
}
