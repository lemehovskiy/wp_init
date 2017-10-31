<?php /* Template Name: {TEMPLATE_NAME} */ ?>

<?php get_header(); ?>


    <div class="main-page-wrap">

        <?php if (have_rows('{FLEXIBLE_FIELD_SLUG}')):
            while (have_rows('{FLEXIBLE_FIELD_SLUG}')) : the_row();

                get_template_part( 'template_parts/{FLEXIBLE_FIELD_SLUG}/' . get_row_layout());

            endwhile;
        endif; ?>

    </div>


<?php get_footer(); ?>