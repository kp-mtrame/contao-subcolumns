<?php

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Felix Pfeiffer : Neue Medien 2007 - 2012
 * @author     Felix Pfeiffer <info@felixpfeiffer.com>
 * @package    Subcolumns
 * @license    CC-A 2.0
 * @filesource
 */

namespace FelixPfeiffer\Subcolumns;

/**
 * Class FormColPart
 *
 * Form field "explanation".
 * @copyright  Felix Pfeiffer : Neue Medien 2010
 * @author     Felix Pfeiffer <info@felixpfeiffer.com>
 * @package    Subcolumns
 */
class FormColEnd extends \Widget
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'form_colset';
	protected $strColTemplate = 'ce_colsetEnd';


	/**
	 * Do not validate
	 */
	public function validate()
	{
		return;
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$this->strSet = $GLOBALS['TL_CONFIG']['subcolumns'] ? $GLOBALS['TL_CONFIG']['subcolumns'] : 'yaml3';
		
		if (TL_MODE == 'BE')
		{
            $arrColor = unserialize($this->fsc_color);

            if(count($arrColor) === 2 && empty($arrColor[1])) {
                $arrColor = '';
            } else {
                $arrColor  = $this->compileColor($arrColor);
            }

            if(!$GLOBALS['TL_SUBCL'][$this->strSet]['files']['css'])
            {
                $this->Template = new \BackendTemplate('be_subcolumns');
                $this->Template->setColor = $this->compileColor($arrColor);
                $this->Template->colsetTitle = '### COLUMNSET START '.$this->fsc_type.' <strong>'.$this->fsc_name.'</strong> ###';

                return $this->Template->parse();
            }

            $GLOBALS['TL_CSS']['subcolumns'] = 'system/modules/Subcolumns/assets/be_style.css';
            $GLOBALS['TL_CSS']['subcolumns_set'] = $GLOBALS['TL_SUBCL'][$this->strSet]['files']['css'];

            $arrColset = $GLOBALS['TL_SUBCL'][$this->strSet]['sets'][$this->fsc_type];
            $strSCClass = $GLOBALS['TL_SUBCL'][$this->strSet]['scclass'];
            $blnInside = $GLOBALS['TL_SUBCL'][$this->strSet]['inside'];

            $intCountContainers = count($GLOBALS['TL_SUBCL'][$this->strSet]['sets'][$this->fsc_type]);

            $strMiniset = '<div class="colsetexample final '.$strSCClass.'">';

            for($i=0;$i<$intCountContainers;$i++)
            {
                $arrPresentColset = $arrColset[$i];
                $strMiniset .= '<div class="'.$arrPresentColset[0].'">'.($blnInside ? '<div class="'.$arrPresentColset[1].'">' : '').($i+1).($blnInside ? '</div>' : '').'</div>';
            }

            $strMiniset .= '</div>';

            $this->Template = new \BackendTemplate('be_subcolumns');
            $this->Template->setColor = $arrColor;
            $this->Template->colsetTitle = '### COLUMNSET START '.$this->fsc_type.' <strong>'.$this->fsc_name.'</strong> ###';
            $this->Template->visualSet = $strMiniset;

            return $this->Template->parse();
		}
		
		$objTemplate = new \FrontendTemplate($this->strColTemplate);
		$objTemplate->useInside = $GLOBALS['TL_SUBCL'][$this->strSet]['inside'];
		return $objTemplate->parse();
	}

    /**
     * Compile a color value and return a hex or rgba color
     * @param mixed
     * @param boolean
     * @param array
     * @return string
     */
    protected function compileColor($color)
    {
        if (!is_array($color))
        {
            return '#' . $this->shortenHexColor($color);
        }
        elseif (!isset($color[1]) || empty($color[1]))
        {
            return '#' . $this->shortenHexColor($color[0]);
        }
        else
        {
            return 'rgba(' . implode(',', $this->convertHexColor($color[0], $blnWriteToFile, $vars)) . ','. ($color[1] / 100) .')';
        }
    }

    /**
     * Try to shorten a hex color
     * @param string
     * @return string
     */
    protected function shortenHexColor($color)
    {
        if ($color[0] == $color[1] && $color[2] == $color[3] && $color[4] == $color[5])
        {
            return $color[0] . $color[2] . $color[4];
        }

        return $color;
    }


    /**
     * Convert hex colors to rgb
     * @param string
     * @param boolean
     * @param array
     * @return array
     * @see http://de3.php.net/manual/de/function.hexdec.php#99478
     */
    protected function convertHexColor($color, $blnWriteToFile=false, $vars=array())
    {
        // Support global variables
        if (strncmp($color, '$', 1) === 0)
        {
            if (!$blnWriteToFile)
            {
                return array($color);
            }
            else
            {
                $color = str_replace(array_keys($vars), array_values($vars), $color);
            }
        }

        $rgb = array();

        // Try to convert using bitwise operation
        if (strlen($color) == 6)
        {
            $dec = hexdec($color);
            $rgb['red'] = 0xFF & ($dec >> 0x10);
            $rgb['green'] = 0xFF & ($dec >> 0x8);
            $rgb['blue'] = 0xFF & $dec;
        }

        // Shorthand notation
        elseif (strlen($color) == 3)
        {
            $rgb['red'] = hexdec(str_repeat(substr($color, 0, 1), 2));
            $rgb['green'] = hexdec(str_repeat(substr($color, 1, 1), 2));
            $rgb['blue'] = hexdec(str_repeat(substr($color, 2, 1), 2));
        }

        return $rgb;
    }
}

?>
