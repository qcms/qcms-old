<?
//-----------------------------------------------------------------------------
// Модуль настройки поиска
//-----------------------------------------------------------------------------

//-----------------------------------------------------------------------------
define("SEARCH_INDEX_DIRNAME", "search_index");


//-----------------------------------------------------------------------------
// $search_config = array(
//	"tables" => array(
//		"table_name" => array(
//			"fields" => array("field1", "field2",),
//			"where" => array("aaa='1'", "bbb<>'2'"),
//			"url" => "url?ii=[field1]&ee=[field2]",
//			"childs" => array(
//				"table_name" => array(
//					"fields" => array("field1", "field2",),
//					"where" => array("aaa='1'", "bbb<>'2'"),
//					"childs" => array(),
//				),
//			),
//		),
//	),
//	"files" => array(
//		"html" => array("file1"=>"url1", "file2"=>"url2",),
//		"php" => array("file1"=>"url1", "file2"=>"url2",),
//	),
// );
//
//
$search_config = array(
	"tables" => array(
		"page" => array(
			"fields" => array("page_name", "page_text", "page_description", "page_keywords",),
			"where" => array("page_isshow='1'",),
			"url" => VN_PAGE."?id=[page_id]",
			"childs" => array(
				"page_media" => array(
					"fields" => array("page_media_name", "page_media_text",),
					"where" => array("page_media_isshow = '1'"),
					"childs" => array(),
				),
			),
		),
	),
	"files" => array(),
	"php" => array(),
);


?>