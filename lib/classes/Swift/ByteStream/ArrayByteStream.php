<?php

/*
 Bi-Directional ByteStream using an array in Swift Mailer.
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
 */

require_once dirname(__FILE__) . '/../InputByteStream.php';
require_once dirname(__FILE__) . '/../OutputByteStream.php';


/**
 * Allows reading and writing of bytes to and from an array.
 * @package Swift
 * @subpackage ByteStream
 * @author Chris Corbyn
 */
class Swift_ByteStream_ArrayByteStream
  implements Swift_InputByteStream, Swift_OutputByteStream
{
  
  /**
   * The internal stack of bytes.
   * @var string[]
   * @access private
   */
  private $_array = array();
  
  /**
   * The internal pointer offset.
   * @var int
   * @access private
   */
  private $_offset = 0;
  
  /**
   * Create a new ArrayByteStream.
   * If $stack is given the stream will be populated with the bytes it contains.
   * @param mixed $stack of bytes in string or array form, optional
   */
  public function __construct($stack = null)
  {
    if (is_array($stack))
    {
      $this->_array = $stack;
    }
    elseif (is_string($stack))
    {
      $this->write($stack);
    }
    else
    {
      $this->_array = array();
    }
  }
  
  /**
   * Reads $length bytes from the stream into a string and moves the pointer
   * through the stream by $length. If less bytes exist than are requested the
   * remaining bytes are given instead. If no bytes are remaining at all, boolean
   * false is returned.
   * @param int $length
   * @return string
   */
  public function read($length)
  {
    if ($this->_offset == count($this->_array))
    {
      return false;
    }
    
    $ret = array_slice($this->_array, $this->_offset, $length);
    $this->_offset += count($ret);
    return implode('', $ret);
  }
  
  /**
   * Writes $bytes to the end of the stream.
   * @param string $bytes
   */
  public function write($bytes)
  {
    foreach (unpack('C*', $bytes) as $byte)
    {
      $this->_array[] = pack('C', $byte);
    }
  }
  
  /**
   * Move the internal read pointer to $byteOffset in the stream.
   * @param int $byteOffset
   * @return boolean
   */
  public function setReadPointer($byteOffset)
  {
    if ($byteOffset > $size = count($this->_array))
    {
      $byteOffset = $size;
    }
    elseif ($byteOffset < 0)
    {
      $byteOffset = 0;
    }
    
    $this->_offset = $byteOffset;
  }
  
  /**
   * Flush the contents of the stream (empty it) and set the internal pointer
   * to the beginning.
   */
  public function flushContents()
  {
    $this->_offset = 0;
    $this->_array = array();
  }
  
}