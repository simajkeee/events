<?php
get_header();
$taxonomy = "event";
$args = [
    "taxonomy" => $taxonomy
];
$eventsWithTaxonomy = [];
if ( !empty( $terms = get_terms( $args ) ) && !( $terms instanceof WP_Error ) ) {
    $now = new DateTime();
    foreach ( $terms as $t ) {
        if ( isTermChild( $t, $taxonomy ) ) {
            continue;
        }

        $allPosts = get_posts([
                    "post_type" => "events",
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'event',
                            'field' => 'term_id',
                            'terms' => $t->term_id,
                            'include_children' => true
                        )
                    )
                ]);
        $events = array_filter( $allPosts, function( $el ) use ( $now ) {
            $postCustomFields = get_fields( $el->ID );
            if ( !isset( $postCustomFields["events_start"] ) ) {
                return false;
            }
            $diff = ( new DateTime( $postCustomFields["events_start"] ) )->diff( $now );
            $totalMonths = $diff->y * 12 + $diff->m;
            return $diff->invert && $totalMonths <= 5;
        });

        if ( empty( $events ) ) {
            continue;
        }
        usort($events, "dateSortEventTaxonomyPosts");
        $eventsWithTaxonomy[ $t->name ] = $events;
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <?php
            if ( empty( $eventsWithTaxonomy ) ) {
        ?>
            <div class="col-12">
                There are no events planning in near future.
            </div>
        <?php
            } else {
                foreach ( $eventsWithTaxonomy as $k => $events ) {
        ?>
            <div class="col-4">
                <h2><?php echo $k . " (total events " . count( $events ) . ")"; ?></h2>
                <ul>
                <?php
                    foreach( $events as $event ) {
                        $postCustomFields = get_fields( $event->ID );
                ?>
                    <li class="mb-2">
                        <div>
                            <h3>
                                <a href="<?php echo get_permalink( $event->ID ); ?>">
                                    <?php echo get_the_title( $event->ID ); ?>
                                </a>
                            </h3>
                            <?php if ( isset( $postCustomFields["events_start"] ) ) { ?>
                            <span>Event starts at: <?php echo ( new DateTime( $postCustomFields["events_start"] ) )->format("F d"); ?></span>
                            <?php } ?>
                        </div>
                    </li>
                <?php
                    }
                ?>
                </ul>
            </div>
        <?php
                }
            }
        ?>
    </div>
</div>
<?php
get_footer();