<?php
/**
 * File containing the optionMatrixType class.
 *
 * @copyright Copyright (C) 2012 Leiden Tech. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version  0.9.1
 * @package optionmatrix
 */

/*!
  \class optionMatrixType optionmatrixtype.php
  \ingroup eZDatatype
  \brief The class optionMatrixType does

*/
class optionMatrixType extends eZDataType
{
    const DEFAULT_NAME_VARIABLE = "_optionmatrix_name_";
    const TYPE_VARIABLE = "_optionmatrix_type_";
    const REQUIRED_VARIABLE = "_optionmatrix_required_";
    const NUM_COLUMNS_VARIABLE = '_optionmatrix_default_num_columns_';
    const NUM_ROWS_VARIABLE = '_optionmatrix_default_num_rows_';
    const CELL_VARIABLE = '_optionmatrix_cell_';
    const DATA_TYPE_STRING = "optionmatrix";


    /*!
     Constructor
    */
    function optionMatrixType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Option matrix", 'Datatype name' ), array( 'serialize_supported' => true ) );
    }

    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        if ( $http->hasPostVariable( $base . "_optionmatrix_name_" . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . '_optionmatrix_type_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $name = $http->postVariable( $base . "_optionmatrix_name_" . $contentObjectAttribute->attribute( "id" ) );
            $typeValue = $http->postVariable( $base . '_optionmatrix_type_' . $contentObjectAttribute->attribute( 'id' ) );
		  $required = $http->postVariable( $base . '_optionmatrix_required_' . $contentObjectAttribute->attribute( 'id' ) );
		$data = false;
		if ( $http->hasPostVariable( $base . '_optionmatrix_cell_' . $contentObjectAttribute->attribute( 'id' ) ) )
			$data = $http->PostVariable( $base . '_optionmatrix_cell_' . $contentObjectAttribute->attribute( 'id' ) );
		$count = 0;
		for ( $i = 0; $i < count( $data ); ++$i )
			if ( trim( $data[$i] ) <> '' )
			{
				++$count;
				break;
			}
		if ( $contentObjectAttribute->validateIsRequired() and ( $count == 0 or $data === false ) )
		{
			$contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
														'Missing optionmatrix input.' ) );
			return eZInputValidator::STATE_INVALID;
		}

            if ( $name == '' or
                 $typeValue == '' )
            {
                if ( ( !$classAttribute->attribute( 'is_information_collector' ) and
                       $contentObjectAttribute->validateIsRequired() ) )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                     'Missing option matrix input.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                    return eZInputValidator::STATE_ACCEPTED;
            }

        }
        else if ( !$classAttribute->attribute( 'is_information_collector' ) and $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Missing option matrix input.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }


    }
    /*!
     Store content
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
        $matrix = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
        $matrix->decodeXML( $contentObjectAttribute->attribute( 'data_text' ) );
        $contentObjectAttribute->setContent( $matrix );
    }

    function storeClassAttribute( $contentClassAttribute, $version )
    {
        $matrixDefinition = $contentClassAttribute->content();
        $contentClassAttribute->setAttribute( 'data_text5', $matrixDefinition->xmlString() );
        $matrixDefinition->decodeClassAttribute( $contentClassAttribute->attribute( 'data_text5' ) );
        $contentClassAttribute->setContent(  $matrixDefinition );
    }


    function objectAttributeContent( $contentObjectAttribute )
    {
        $option = new optionMatrix( "" );
        $option->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $option;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $matrix = $contentObjectAttribute->content();
        $columnsArray = $matrix->attribute( 'columns' );
        $columns = $columnsArray['sequential'];
        $count = 0;
        foreach ( $columns as $column )
        {
            $count += count( $column['rows'] );
        }
        return $count > 0;
    }

    function metaData( $contentObjectAttribute )
    {
        $matrix = $contentObjectAttribute->content();
        $columnsArray = $matrix->attribute( 'columns' );
        $columns = $columnsArray['sequential'];
        $metaDataArray = array();
        foreach ( $columns as $column )
        {
            $rows = $column['rows'];
            foreach ( $rows as $row )
            {
                $metaDataArray[] = array( 'id' => $column['identifier'],
                                          'text' => $row );
            }
        }
        return $metaDataArray;
    }

    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
		$optionName = $http->postVariable( $base . "_optionmatrix_name_" . $contentObjectAttribute->attribute( "id" ) );
		if ( $http->hasPostVariable( $base . "_data_optionmatrix_id_" . $contentObjectAttribute->attribute( "id" ) ) )
			$optionIDArray = $http->postVariable( $base . "_data_optionmatrix_id_" . $contentObjectAttribute->attribute( "id" ) );
		else
			$optionIDArray = array();

        $cellsVarName = $base . self::CELL_VARIABLE . $contentObjectAttribute->attribute( 'id' );
            $matrix = $contentObjectAttribute->attribute( 'content' );
        if ( $http->hasPostVariable( $cellsVarName ) )
        {
            $cells = array();
            foreach ( $http->postVariable( $cellsVarName ) as $cell )
            {
                $cells[] = $cell;
            }
            $matrix->Cells = $cells;
		}
		$matrix->Name = $optionName;

		$optionTypeValue = $http->postVariable( $base . "_optionmatrix_type_" . $contentObjectAttribute->attribute( "id" ) );
		$requiredValue = $http->postVariable( $base . "_optionmatrix_required_" . $contentObjectAttribute->attribute( "id" ) );
		$matrix->setTypeValue( $optionTypeValue );
		$matrix->setRequiredValue( $requiredValue );

		$contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
          $matrix->decodeXML( $contentObjectAttribute->attribute( 'data_text' ) );
		$contentObjectAttribute->setContent( $matrix );
        return true;
    }

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        switch ( $action )
        {
            case 'new_row' :
            {
                $matrix = $contentObjectAttribute->content();
                $postvarname = 'ContentObjectAttribute' . '_data_optionmatrix_remove_' . $contentObjectAttribute->attribute( 'id' );
                $addCountName = 'ContentObjectAttribute' . '_data_optionmatrix_add_count_' . $contentObjectAttribute->attribute( 'id' );

                $addCount = 1;
                if ( $http->hasPostVariable( $addCountName ) )
                {
                    $addCount = $http->postVariable( $addCountName );
                }

                if ( $http->hasPostVariable( $postvarname ) )
                {
                    $selected = $http->postVariable( $postvarname );
                    $matrix->addRow( $selected[0], $addCount );
                }
                else
                {
                    $matrix->addRow( false, $addCount );
                }

                $contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
                $matrix->decodeXML( $contentObjectAttribute->attribute( 'data_text' ) );
                $contentObjectAttribute->setContent( $matrix );
                $contentObjectAttribute->store();
            }break;
            case 'remove_selected' :
            {
                $matrix = $contentObjectAttribute->content( );
                $postvarname = 'ContentObjectAttribute' . '_data_optionmatrix_remove_' . $contentObjectAttribute->attribute( 'id' );
                $arrayRemove = $http->postVariable( $postvarname );
                rsort( $arrayRemove );
                foreach ( $arrayRemove as $rowNum)
                {
                    $matrix->removeRow( $rowNum );
                }

                $contentObjectAttribute->setAttribute( 'data_text', $matrix->xmlString() );
                $matrix->decodeXML( $contentObjectAttribute->attribute( 'data_text' ) );
                $contentObjectAttribute->setContent( $matrix );
                $contentObjectAttribute->store();
            }break;
            default :
            {
                eZDebug::writeError( 'Unknown custom HTTP action: ' . $action, 'eZMatrixType' );
            }break;
        }
    }
    function title( $contentObjectAttribute, $name = null )
    {
        $option = new optionMatrix( "" );
        $option->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
        return $option->attribute('name');
    }


    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
		if ( $currentVersion == false )
		{
			$matrix = $contentObjectAttribute->content();
			$contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
			if ( !$matrix )
			{
				$matrix = new optionMatrix( $contentClassAttribute->attribute( 'data_text1' ) );

			}
			$matrix->setName( $contentClassAttribute->attribute( 'data_text1' ) );
			$matrix->setTypeValue( $contentClassAttribute->attribute( 'data_text2' ) );
			$matrix->setRequiredValue( $contentClassAttribute->attribute( 'data_text3' ) );
			$matrix->addRow( false, $contentClassAttribute->attribute( 'data_int1' ));
			$matrix->adjustColumnsToDefinition( $contentClassAttribute->attribute( 'content' ) );
		} else {
			$matrix = $originalContentObjectAttribute->content();
			$contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
			$matrix->adjustColumnsToDefinition( $contentClassAttribute->attribute( 'content' ) );
		}
		$contentObjectAttribute->setAttribute( "data_text", $matrix->xmlString() );
		$contentObjectAttribute->setContent( $matrix );
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $dataFetched = false;
        $nameName = $base . self::DEFAULT_NAME_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $nameName ) )
        {
            $name = $http->postVariable( $nameName );
            $classAttribute->setAttribute( 'data_text1', $name );
            $dataFetched = true;
        }
        $typeName = $base . self::TYPE_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $typeName ) )
        {
            $type = $http->postVariable( $typeName );
            if ( $type == "" ) $type = 0;
            $classAttribute->setAttribute( 'data_text2', $type );
            $dataFetched = true;
        }
        $requiredName = $base . self::REQUIRED_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $requiredName ) )
        {
            $required = $http->postVariable( $requiredName );
            $classAttribute->setAttribute( 'data_text3',$required );
            $dataFetched = true;
        } else {
            $classAttribute->setAttribute( 'data_text3',"" );
        }

        $defaultNumColumnsName = $base . self::NUM_COLUMNS_VARIABLE . $classAttribute->attribute( 'id' );
        $defaultNumRowsName = $base . self::NUM_ROWS_VARIABLE . $classAttribute->attribute( 'id' );


        if ( $http->hasPostVariable( $defaultNumRowsName ) )
        {
            $defaultNumRowsValue = $http->postVariable( $defaultNumRowsName );

            if ( $defaultNumRowsValue == '' )
            {
                $defaultNumRowsValue = '1';
            }
            $classAttribute->setAttribute( 'data_int1', $defaultNumRowsValue );
            $dataFetched = true;
        }

        $columnNameVariable = $base . '_data_optionmatrix_column_name_' . $classAttribute->attribute( 'id' );
        $columnIDVariable = $base . '_data_optionmatrix_column_id_' . $classAttribute->attribute( 'id' );


        if ( $http->hasPostVariable( $columnNameVariable ) && $http->hasPostVariable( $columnIDVariable ) )
        {
            $columns = array();
            $i = 0;
            $columnNameList = $http->postVariable( $columnNameVariable );
            $columnIDList = $http->postVariable( $columnIDVariable );

            $matrixDefinition = $classAttribute->attribute( 'content' );
            $columnNames = $matrixDefinition->attribute( 'columns' );
            foreach ( $columnNames as $columnName )
            {
                $columnID = '';
                $name = '';
                $index = $columnName['index'];

                // after adding a new column $columnIDList and $columnNameList doesn't contain values for new column.
                // if so just add column with empty 'name' and 'columnID'.
                if ( isset( $columnIDList[$index] ) && isset( $columnNameList[$index] ) )
                {
                    $columnID = $columnIDList[$index];
                    $name = $columnNameList[$index];
                    if ( strlen( $columnID ) == 0 )
                    {
                        $columnID = $name;
                        // Initialize transformation system
                        $trans = eZCharTransform::instance();
                        $columnID = $trans->transformByGroup( $columnID, 'identifier' );
                    }
                }

                $columns[] = array( 'name' => $name,
                                    'identifier' => $columnID,
                                    'index' => $i );

                $i++;
            }

            $matrixDefinition->ColumnNames = $columns;
            $classAttribute->setContent( $matrixDefinition );
            $classAttribute->setAttribute( 'data_text5', $matrixDefinition->xmlString() );

            $dataFetched = true;
        }
        if ( $dataFetched ) {
		return true;
        }
        return false;
    }

    function preStoreClassAttribute( $classAttribute, $version )
    {
        $matrixDefinition = $classAttribute->attribute( 'content' );
        $classAttribute->setAttribute( 'data_text5', $matrixDefinition->xmlString() );
    }

    /*!
     Returns the content.
    */

    function classAttributeContent( $contentClassAttribute )
    {
        $matrixDefinition = new optionMatrixDefinition();
        $matrixDefinition->decodeClassAttribute( $contentClassAttribute->attribute( 'data_text5' ) );
        return $matrixDefinition;
    }

    function customClassAttributeHTTPAction( $http, $action, $contentClassAttribute )
    {
        $id = $contentClassAttribute->attribute( 'id' );
        switch ( $action )
        {
            case 'new_optionmatrix_column' :
            {
                $matrixDefinition = $contentClassAttribute->content();
                $matrixDefinition->addColumn( '' );
                $contentClassAttribute->setContent( $matrixDefinition );
                $contentClassAttribute->store();
            }break;
            case 'remove_selected' :
            {
                $matrix = $contentClassAttribute->content( );
                $postvarname = 'ContentClass' . '_data_optionmatrix_column_remove_' . $contentClassAttribute->attribute( 'id' );
                $array_remove = $http->postVariable( $postvarname );
                foreach( $array_remove as $columnIndex )
                {
                    $matrix->removeColumn( $columnIndex );
                }
                $contentClassAttribute->setContent( $matrix );
                $contentClassAttribute->store();
            }break;
            default :
            {
                eZDebug::writeError( 'Unknown custom HTTP action: ' . $action, 'eZEnumType' );
            }break;
        }
    }

    function isIndexable()
    {
        return true;
    }

    function toString( $contentObjectAttribute )
    {
        $matrix = $contentObjectAttribute->attribute( 'content' );
        $matrixArray = array();
        $rows = $matrix->attribute( 'rows' );

        foreach( $rows['sequential'] as $row )
        {
            $matrixArray[] = eZStringUtils::implodeStr( $row['columns'], '|' );
        }

        return eZStringUtils::implodeStr( $matrixArray, '&' );
/*THIS IS WRONG - THREE LEVELS OF "|" and "&" */
        $option = $contentObjectAttribute->attribute( 'content' );
        $optionArray = array();
        $optionArray[] = $option->attribute( 'name' );
        $optionArray[] = $option->attribute( 'type_value' );
        $optionArray[] = $option->attribute( 'required_value' );

        $rows = $option->attribute( 'rows' );

        foreach( $rows['sequential'] as $row )
        {
            $optionArray[] = eZStringUtils::implodeStr( $row['columns'], '&' );
        }

        return eZStringUtils::implodeStr( $optionArray, '|' );
    }


    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string != '' )
        {
            $matrix = $contentObjectAttribute->attribute( 'content' );
            $matrixRowsList = eZStringUtils::explodeStr( $string, "&" );
            $cells = array();
            $matrix->Matrix['rows']['sequential'] = array();
            $matrix->NumRows = 0;

            foreach( $matrixRowsList as $key => $value )
            {
                $newCells = eZStringUtils::explodeStr( $value, '|' );
                $matrixArray[] = $newCells;
                $cells = array_merge( $cells, $newCells );

                $newRow['columns'] = $newCells;
                $newRow['identifier'] =  'row_' . ( $matrix->NumRows + 1 );
                $newRow['name'] = 'Row_' . ( $matrix->NumRows + 1 );
                $matrix->NumRows++;


                $matrix->Matrix['rows']['sequential'][] = $newRow;
            }
            $matrix->Cells = $cells;
        }
        return true;
/*THIS IS WRONG - THREE LEVELS OF "|" and "&" */
        if ( $string == '' )
            return true;

        $optionArray = eZStringUtils::explodeStr( $string, "|" );

        $option = new optionMatrix( '' );

        $option->Name = array_shift( $optionArray );
        $option->typeValue = array_shift( $optionArray );
        $option->requiredValue = array_shift( $optionArray );
            $matrixRowsList = eZStringUtils::explodeStr( $string, "&" );
            $cells = array();
            $option->Matrix['rows']['sequential'] = array();
            $option->NumRows = 0;

            foreach( $matrixRowsList as $key => $value )
            {
                $newCells = eZStringUtils::explodeStr( $value, '|' );
                $matrixArray[] = $newCells;
                $cells = array_merge( $cells, $newCells );

                $newRow['columns'] = $newCells;
                $newRow['identifier'] =  'row_' . ( $option->NumRows + 1 );
                $newRow['name'] = 'Row_' . ( $option->NumRows + 1 );
                $option->NumRows++;


                $option->Matrix['rows']['sequential'][] = $newRow;
            }
            $option->Cells = $cells;
        //return true;
//Hmm I think this is the answer
        $contentObjectAttribute->setAttribute( "data_text", $option->xmlString() );
        return $option;

    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
       $content = $classAttribute->content();
        if ( $content )
        {
            $defaultName = $classAttribute->attribute( 'data_text1' );
            $defaultRowCount = $classAttribute->attribute( 'data_int1' );
            $columns = $content->attribute( 'columns' );

            $dom = $attributeParametersNode->ownerDocument;
            $defaultNameNode = $dom->createElement( 'default-name' );
            $defaultNameNode->appendChild( $dom->createTextNode( $defaultName ) );
            $attributeParametersNode->appendChild( $defaultNameNode );
            $defaultRowCountNode = $dom->createElement( 'default-row-count' );
            $defaultRowCountNode->appendChild( $dom->createTextNode( $defaultRowCount ) );
            $attributeParametersNode->appendChild( $defaultRowCountNode );
            $columnsNode = $dom->createElement( 'columns' );
            $attributeParametersNode->appendChild( $columnsNode );
            foreach ( $columns as $column )
            {
                unset( $columnNode );
                $columnNode = $dom->createElement( 'column' );
                $columnNode->setAttribute( 'name', $column['name'] );
                $columnNode->setAttribute( 'identifier', $column['identifier'] );
                $columnNode->setAttribute( 'index', $column['index'] );
                $columnsNode->appendChild( $columnNode );
            }
        }
        $defaultName = $classAttribute->attribute( 'data_text1' );
        $dom = $attributeParametersNode->ownerDocument;
        $defaultNameNode = $dom->createElement( 'default-name' );
        $defaultNameNode->appendChild( $dom->createTextNode( $defaultName ) );
        $attributeParametersNode->appendChild( $defaultNameNode );

    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultName = $attributeParametersNode->getElementsByTagName( 'default-name' )->item( 0 )->textContent;
        $classAttribute->setAttribute( 'data_text1', $defaultName );
        $defaultRowCount = $attributeParametersNode->getElementsByTagName( 'default-row-count' )->item( 0 )->textContent;
        $classAttribute->setAttribute( 'data_int1', $defaultRowCount );

        $matrixDefinition = new eZMatrixDefinition();
        $columnsNode = $attributeParametersNode->getElementsByTagName( 'columns' )->item( 0 );
        $columnsList = $columnsNode->getElementsByTagName( 'column' );
        foreach ( $columnsList  as $columnNode )
        {
            $columnName = $columnNode->getAttribute( 'name' );
            $columnIdentifier = $columnNode->getAttribute( 'identifier' );
            $matrixDefinition->addColumn( $columnName, $columnIdentifier );
        }
        $classAttribute->setContent( $matrixDefinition );
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        $domDocument = new DOMDocument( '1.0', 'utf-8' );
        $success = $domDocument->loadXML( $objectAttribute->attribute( 'data_text' ) );

        $importedRoot = $node->ownerDocument->importNode( $domDocument->documentElement, true );
        $node->appendChild( $importedRoot );

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $rootNode = $attributeNode->getElementsByTagName( 'optionmatrix' )->item( 0 );
        $xmlString = $rootNode ? $rootNode->ownerDocument->saveXML( $rootNode ) : '';
        $objectAttribute->setAttribute( 'data_text', $xmlString );
    }
    function isInformationCollector()
    {
        return true;
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    function batchInitializeObjectAttributeData( $classAttribute )
    {
        $numRows = $classAttribute->attribute( 'data_int1' );
        $matrix = new optionMatrix( '', $numRows, $classAttribute->attribute( 'content' ) );
        $db = eZDB::instance();
        return array( 'data_text' => "'" . $db->escapeString( $matrix->xmlString() ) . "'" );
    }
}

eZDataType::register( optionMatrixType::DATA_TYPE_STRING, "optionMatrixType" );

?>
