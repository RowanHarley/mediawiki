<?php

function wfSpecialUnusedimages() {
	global $wgUser, $wgOut, $wgLang, $wgTitle;
	$fname = "wfSpecialUnusedimages";

	list( $limit, $offset ) = wfCheckLimits();

	$sql = "SELECT img_name,img_user,img_user_text,img_timestamp,img_description " .
	  "FROM image LEFT JOIN imagelinks ON img_name=il_to WHERE il_to IS NULL " .
	  "ORDER BY img_timestamp ".wfLimitResult($limit,$offset);
	$res = wfQuery( $sql, DB_READ, $fname );

	$sk = $wgUser->getSkin();

	$wgOut->addHTML( wfMsg( "unusedimagestext" ) );
	$top = wfShowingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = wfViewPrevNext( $offset, $limit,
	  $wgLang->specialPage( "Unusedimages" ) );
	$wgOut->addHTML( "<br />{$sl}</p>\n" );

	$ins = $wgLang->getNsText ( 6 ) ;
	$s = "<ol start='" . ( $offset + 1 ) . "'>";
	while ( $obj = wfFetchObject( $res ) ) {
		$name = $obj->img_name;
		$dlink = $sk->makeKnownLink( "{$ins}:{$name}", wfMsg( "imgdesc" ) );
		$ilink = "<a href=\"" . htmlspecialchars( Image::wfImageUrl( $name ) ) .
			"\">" . htmlspecialchars( $name ) . "</a>";

		$d = $wgLang->timeanddate( $obj->img_timestamp, true );
		$u = $obj->img_user;
		$ut = $obj->img_user_text;
		$c = $sk->formatComment( $obj->img_description );

		if ( 0 == $u ) { $ul = $ut; }
		else { $ul = $sk->makeLink( $wgLang->getNsText(2).":{$ut}", $ut ); }

		$s .= "<li>({$dlink}) {$ilink} . . {$d} . . {$ul}";

		if ( "" != $c && "*" != $c ) { $s .= " <em>({$c})</em>"; }
		$s .= "</li>\n";
	}
	wfFreeResult( $res );
	$s .= "</ol>\n\n";
	$wgOut->addHTML( $s );
	$wgOut->addHTML( "<p>{$sl}</p>\n" );
}

?>
