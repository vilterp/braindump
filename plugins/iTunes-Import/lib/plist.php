<?php
// from http://blog.iconara.net/2007/05/08/php-plist-parsing/
// wrapped in a class, Spyc-style, by Pete Vilter

// usage: $data = Plist::parse('path/to/plist.xml');

class Plist {
  
  function parse($path) {
    $plistDocument = new DOMDocument();
    $plistDocument->load($path);
    return self::parsePlist($plistDocument);
  }
  
  function parsePlist( $document ) {
    $plistNode = $document->documentElement;

    $root = $plistNode->firstChild;

    // skip any text nodes before the first value node
    while ( $root->nodeName == "#text" ) {
      $root = $root->nextSibling;
    }

    return self::parseValue($root);
  }

  function parseValue( $valueNode ) {
    $valueType = $valueNode->nodeName;

    $transformerName = "parse_$valueType";

    if ( is_callable(array('Plist',$transformerName)) ) {
      // there is a transformer function for this node type
      return call_user_func(array('Plist',$transformerName), $valueNode);
    }

    // if no transformer was found
    return null;
  }

  function parse_integer( $integerNode ) {
  	return $integerNode->textContent;
  }

  function parse_string( $stringNode ) {
  	return $stringNode->textContent;
  }

  function parse_date( $dateNode ) {
  	return $dateNode->textContent;
  }

  function parse_true( $trueNode ) {
  	return true;
  }

  function parse_false( $trueNode ) {
  	return false;
  }

  function parse_dict( $dictNode ) {
    $dict = array();

    // for each child of this node
    for (
      $node = $dictNode->firstChild;
      $node != null;
      $node = $node->nextSibling
    ) {
      if ( $node->nodeName == "key" ) {
        $key = $node->textContent;

        $valueNode = $node->nextSibling;

        // skip text nodes
        while ( $valueNode->nodeType == XML_TEXT_NODE ) {
          $valueNode = $valueNode->nextSibling;
        }

        // recursively parse the children
        $value = self::parseValue($valueNode);

        $dict[$key] = $value;
      }
    }

    return $dict;
  }

  function parse_array( $arrayNode ) {
    $array = array();

    for (
      $node = $arrayNode->firstChild;
      $node != null;
      $node = $node->nextSibling
    ) {
      if ( $node->nodeType == XML_ELEMENT_NODE ) {
        array_push($array, self::parseValue($node));
      }
    }

    return $array;
  }
}

?>