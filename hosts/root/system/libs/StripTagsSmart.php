<?php
/**
 * Более продвинутый аналог strip_tags() для корректного вырезания тагов из html кода.
 * Функция strip_tags(), в зависимости от контекста, может работать не корректно.
 * Возможности:
 *   - корректно обрабатываются вхождения типа "a < b > c"
 *   - корректно обрабатывается "грязный" html, когда в значениях атрибутов тагов могут встречаться символы < >
 *   - корректно обрабатывается разбитый html
 *   - вырезаются комментарии, скрипты, стили, PHP, Perl, ASP код, MS Word таги, CDATA
 *   - автоматически форматируется текст, если он содержит html код
 *   - защита от подделок типа: "<<fake>script>alert('hi')</</fake>script>"
 *
 * @param   string  $s
 * @param   array   $allowable_tags     Массив тагов, которые не будут вырезаны
 *                                      Пример: 'b' -- таг останется с атрибутами, '<b>' -- таг останется без атрибутов
 * @param   bool    $is_format_spaces   Форматировать пробелы и переносы строк?
 *                                      Вид текста на выходе (plain) максимально приближеется виду текста в браузере на входе.
 *                                      Другими словами, грамотно преобразует text/html в text/plain.
 *                                      Текст форматируется только в том случае, если были вырезаны какие-либо таги.
 * @param   array   $pair_tags   массив имён парных тагов, которые будут удалены вместе с содержимым
 *                               см. значения по умолчанию
 * @param   array   $para_tags   массив имён парных тагов, которые будут восприниматься как параграфы (если $is_format_spaces = true)
 *                               см. значения по умолчанию
 * @return  string
 *
 * @license  http://creativecommons.org/licenses/by-sa/3.0/
 * @author   Nasibullin Rinat, http://orangetie.ru/
 * @charset  ANSI
 * @version  4.0.14
 * http://forum.dklab.ru/viewtopic.php?t=19849&postdays=0&postorder=asc&start=0
 */
function StripTagsSmart(
    /*string*/ $s,
    array $allowable_tags = null,
    /*boolean*/ $is_format_spaces = true,
    array $pair_tags = array('script', 'style', 'map', 'iframe', 'frameset', 'object', 'applet', 'comment', 'button', 'textarea', 'select'),
    array $para_tags = array('p', 'td', 'th', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'form', 'title', 'pre')
)
{
    //return strip_tags($s);
    static $_callback_type  = false;
    static $_allowable_tags = array();
    static $_para_tags      = array();
    #regular expression for tag attributes
    #correct processes dirty and broken HTML in a singlebyte or multibyte UTF-8 charset!
    static $re_attrs_fast_safe =  '(?![a-zA-Z\d])  #statement, which follows after a tag
                                   #correct attributes
                                   (?>
                                       [^>"\']+
                                     | (?<=[\=\x20\r\n\t]|\xc2\xa0) "[^"]*"
                                     | (?<=[\=\x20\r\n\t]|\xc2\xa0) \'[^\']*\'
                                   )*
                                   #incorrect attributes
                                   [^>]*+';

    if (is_array($s))
    {
        if ($_callback_type === 'strip_tags')
        {
            $tag = strtolower($s[1]);
            if ($_allowable_tags)
            {
                #tag with attributes
                if (array_key_exists($tag, $_allowable_tags)) return $s[0];

                #tag without attributes
                if (array_key_exists('<' . $tag . '>', $_allowable_tags))
                {
                    if (substr($s[0], 0, 2) === '</') return '</' . $tag . '>';
                    if (substr($s[0], -2) === '/>')   return '<' . $tag . ' />';
                    return '<' . $tag . '>';
                }
            }
            if ($tag === 'br') return "\r\n";
            if ($_para_tags && array_key_exists($tag, $_para_tags)) return "\r\n\r\n";
            return '';
        }
        trigger_error('Unknown callback type "' . $_callback_type . '"!', E_USER_ERROR);
    }

    if (($pos = strpos($s, '<')) === false || strpos($s, '>', $pos) === false)  #speed improve
    {
        #tags are not found
        return $s;
    }

    $length = strlen($s);

    #unpaired tags (opening, closing, !DOCTYPE, MS Word namespace)
    $re_tags = '~  <[/!]?+
                   (
                       [a-zA-Z][a-zA-Z\d]*+
                       (?>:[a-zA-Z][a-zA-Z\d]*+)?
                   ) #1
                   ' . $re_attrs_fast_safe . '
                   >
                ~sxSX';

    $patterns = array(
        '/<([\?\%]) .*? \\1>/sxSX',     #встроенный PHP, Perl, ASP код
        '/<\!\[CDATA\[ .*? \]\]>/sxSX', #блоки CDATA
        #'/<\!\[  [\x20\r\n\t]* [a-zA-Z] .*?  \]>/sxSX',  #:DEPRECATED: MS Word таги типа <![if! vml]>...<![endif]>

        '/<\!--.*?-->/sSX', #комментарии

        #MS Word таги типа "<![if! vml]>...<![endif]>",
        #условное выполнение кода для IE типа "<!--[if expression]> HTML <![endif]-->"
        #условное выполнение кода для IE типа "<![if expression]> HTML <![endif]>"
        #см. http://www.tigir.com/comments.htm
        '/ <\! (?:--)?+
               \[
               (?> [^\]"\']+ | "[^"]*" | \'[^\']*\' )*
               \]
               (?:--)?+
           >
         /sxSX',
    );
    if ($pair_tags)
    {
        #парные таги вместе с содержимым:
        foreach ($pair_tags as $k => $v) $pair_tags[$k] = preg_quote($v, '/');
        $patterns[] = '/ <((?i:' . implode('|', $pair_tags) . '))' . $re_attrs_fast_safe . '(?<!\/)>
                         .*?
                         <\/(?i:\\1)' . $re_attrs_fast_safe . '>
                       /sxSX';
    }
    #d($patterns);

    $i = 0; #защита от зацикливания
    $max = 99;
    while ($i < $max)
    {
        $s2 = preg_replace($patterns, '', $s);
        if (preg_last_error() !== PREG_NO_ERROR)
        {
            $i = 999;
            break;
        }

        if ($i == 0)
        {
            $is_html = ($s2 != $s || preg_match($re_tags, $s2));
            if (preg_last_error() !== PREG_NO_ERROR)
            {
                $i = 999;
                break;
            }
            if ($is_html)
            {
                if ($is_format_spaces)
                {
                    /*
                    В библиотеке PCRE для PHP \s - это любой пробельный символ, а именно класс символов [\x09\x0a\x0c\x0d\x20\xa0] или, по другому, [\t\n\f\r \xa0]
                    Если \s используется с модификатором /u, то \s трактуется как [\x09\x0a\x0c\x0d\x20]
                    Браузер не делает различия между пробельными символами, друг за другом подряд идущие символы воспринимаются как один
                    */
                    #$s2 = str_replace(array("\r", "\n", "\t"), ' ', $s2);
                    #$s2 = strtr($s2, "\x09\x0a\x0c\x0d", '    ');
                    $s2 = preg_replace('/  [\x09\x0a\x0c\x0d]++
                                         | <((?i:pre|textarea))' . $re_attrs_fast_safe . '(?<!\/)>
                                           .+?
                                           <\/(?i:\\1)' . $re_attrs_fast_safe . '>
                                           \K
                                        /sxSX', ' ', $s2);
                    if (preg_last_error() !== PREG_NO_ERROR)
                    {
                        $i = 999;
                        break;
                    }
                }

                #массив тагов, которые не будут вырезаны
                if ($allowable_tags) $_allowable_tags = array_flip($allowable_tags);

                #парные таги, которые будут восприниматься как параграфы
                if ($para_tags) $_para_tags = array_flip($para_tags);
            }
        }#if

        #tags processing
        if ($is_html)
        {
            $_callback_type = 'strip_tags';
            $s2 = preg_replace_callback($re_tags, __FUNCTION__, $s2);
            $_callback_type = false;
            if (preg_last_error() !== PREG_NO_ERROR)
            {
                $i = 999;
                break;
            }
        }

        if ($s === $s2) break;
        $s = $s2; $i++;
    }#while
    if ($i >= $max) $s = strip_tags($s); #too many cycles for replace...

    if ($is_format_spaces && strlen($s) !== $length)
    {
        #remove a duplicate spaces
        $s = preg_replace('/\x20\x20++/sSX', ' ', trim($s));
        #remove a spaces before and after new lines
        $s = str_replace(array("\r\n\x20", "\x20\r\n"), "\r\n", $s);
        #replace 3 and more new lines to 2 new lines
        $s = preg_replace('/[\r\n]{3,}+/sSX', "\r\n\r\n", $s);
    }
    return $s;
}
?>
