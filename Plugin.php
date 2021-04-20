<?php

/**
 * Rainiar's Toolkit for Typecho - Crazy Text
 * 
 * @package RnRCrazyText
 * @author Rainiar
 * @version 1.0.0
 * @link https://rainiar.top
 */

class RnRCrazyText_EB extends Typecho_Widget_Helper_Form_Element {
    public function input($name = NULL, array $options = NULL) {}
    protected function _value($value) {}
}

class RnRCrazyText_Plugin implements Typecho_Plugin_Interface {
    static $color = array(
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

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx_980699401 = array('RnRCrazyText_Plugin', 'contentEx');
    }

    public static function contentEx($content, Widget_Abstract_Contents $contents, $last) {
        $content = empty($last) ? $content : $last;
        return preg_replace_callback(
            '@([\s\S]{0,1})\[(style)=([\S]+?)\]([\s\S]*?)\[/style\]@',
            function ($a) {
                if ($a[1] == '\\')
                    return $a[2] == 'style' ? substr($a[0], 1) : $a[0];
                preg_match_all('@[0-9a-zA-Z#$]+@', $a[3], $matches);
                array_unique($matches[0]);
                $style = '';
                $isctr = false;
                $isdel = false;
                $isunder = false;
                $isscr = false;
                $isbold = false;
                $isfcol = false;
                $isbcol = false;
                foreach ($matches[0] as $key) {
                    if ($key == 'center')
                        $isctr = true;
                    else if ($key == 'delete')
                        $isdel = true;
                    else if ($key == 'underline')
                        $isunder = true;
                    else if ($key == 'secret' && !$isfcol && !$isbcol) {
                        $isscr = true;
                        $style .= 'background-color:#000000;color:#000000';
                        $isfcol = true;
                        $isbcol = true;
                    }
                    else if ($key == 'bold')
                        $isbold = true;
                    else if ($key[0] == '#' && !$isfcol) {
                        if (array_key_exists(substr($key, 1), self::$color)) {
                            $style .= 'color:' . self::$color[substr($key, 1)] . ';';
                            $isfcol = true;
                        }
                        else if (preg_match('@^#[0-9a-fA-F]{6}$@', substr($key, 1))) {
                            $style .= 'color:' . $key . ';';
                            $isfcol = true;
                        }
                    }
                    else if ($key[0] == '$' && !$isbcol) {
                        if (array_key_exists(substr($key, 1), self::$color)) {
                            $style .= 'background-color:' . self::$color[substr($key, 1)] . ';';
                            $isbcol = true;
                        }
                        else if (preg_match('@^#[0-9a-fA-F]{6}$@', substr($key, 1))) {
                            $style .= 'background-color:' . $key . ';';
                            $isbcol = true;
                        }
                    }
                }
                $spn = '<span style="' . $style . ($isscr ? '" title="你知道的太多了"' : '"') .'>' . $a[4] . '</span>';
                if ($isdel)
                    $spn = '<del>' . $spn . '</del>';
                if ($isunder)
                    $spn = '<u>' . $spn . '</u>';
                if ($isbold)
                    $spn = '<b>' . $spn . '</b>';
                if ($isctr)
                    $spn = '<p style="text-align:center;">' . $spn . '</p>';
                return $a[1] . $spn;
            },
            $content);
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
    <p>[style=<u style="color:gray;">&lt;文字属性&gt;</u>]<u style="color:gray;">&lt;内容&gt;</u>[/style]</p>
    <p>文字属性内部及前后无多余空空格</p>
    <p>如果不希望短代码生效，在该短代码前添加反斜杠即可</p>
    <h3>文字属性</h3>
    <p>文字属性由多个值组成，由英文逗号分隔</p>
    <p>center：居中</p>
    <p>delete：删除线</p>
    <p>underline：下划线</p>
    <p>secret：秘密文字（黑底黑字，你知道的太多了）</p>
    <p>bold：加粗</p>
    <p>#：在井号后跟随颜色代码或者6位十六进制数，表示文字颜色</p>
    <p>$：在美元号后跟随颜色代码或者6位十六进制数，表示文字背景颜色</p>
    <h3>例子</h3>
    <p>[style=#008000,$yellow,center,delete,underline,bold]This is a weird text.[/style]</p>
    <p>这是绿色文字，黄色背景，居中，带有删除线和下划线的加粗字</p>
    <h3>颜色代码</h3>
    <style>
        .center_text {text-align:center;}
        .odd_col {background:#cccccc}
        .even_col {background-color:#dfdfdf}
        .odd_coll {background:#eeeeee}
        .even_coll {background:#efefef}
    </style>
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
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}
}
