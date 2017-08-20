<?php

/**
 * Author: Patrick Benny, Tino Reichardt
 * License: FPDF
 *
 * Source: http://fpdf.org/en/script/
 */

require_once('class.fpdf.php');

class PDF extends FPDF {

  function SetDash($black = null, $white = null) {
    if ($black !== null) {
      $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
    } else {
      $s='[] 0 d';
    }
    $this->_out($s);
  }

  function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link='')
  {
    if ($txt != '') {
      $txt = utf8_decode($txt);
    }
    parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
  }

  function Text($x, $y, $txt)
  {
    parent::Text($x, $y, utf8_decode($txt));
  }

  //Cell with horizontal scaling if text is too wide
  function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
  {
      //Get string width
      $str_width=$this->GetStringWidth($txt);

      //Calculate ratio to fit cell
      if($w==0)
          $w = $this->w-$this->rMargin-$this->x;
      $ratio = ($w-$this->cMargin*2)/$str_width;

      $fit = ($ratio < 1 || ($ratio > 1 && $force));
      if ($fit)
      {
          if ($scale)
          {
              //Calculate horizontal scaling
              $horiz_scale=$ratio*100.0;
              //Set horizontal scaling
              $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
          }
          else
          {
              //Calculate character spacing in points
              $char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
              //Set character spacing
              $this->_out(sprintf('BT %.2F Tc ET',$char_space));
          }
          //Override user alignment (since text will fill up cell)
          $align='';
      }

      //Pass on to Cell method
      $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);

      //Reset character spacing/horizontal scaling
      if ($fit)
          $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
  }

  //Cell with horizontal scaling only if necessary
  function CellFitScale($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
  {
      $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,false);
  }

  //Cell with horizontal scaling always
  function CellFitScaleForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
  {
      $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,true);
  }

  //Cell with character spacing only if necessary
  function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
  {
      $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
  }

  //Cell with character spacing always
  function CellFitSpaceForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
  {
      //Same as calling CellFit directly
      $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,true);
  }

  //Patch to also work with CJK double-byte text
  function MBGetStringLength($s)
  {
      if($this->CurrentFont['type']=='Type0')
      {
          $len = 0;
          $nbbytes = strlen($s);
          for ($i = 0; $i < $nbbytes; $i++)
          {
              if (ord($s[$i])<128)
                  $len++;
              else
              {
                  $len++;
                  $i++;
              }
          }
          return $len;
      }
      else
          return strlen($s);
  }
}

?>
