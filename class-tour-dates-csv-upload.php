<?php
class TourDatesCsvUpload {

    public function __construct(){
        add_action('acf/save_post', array($this, 'group_tour_date_save'), 20);
    }

    // On post save
    public function group_tour_date_save(){
        
        if( !isset($_POST['post_ID']) ) return;
        if( !isset($_POST['post_type']) ) return;
        
        $post_id = $_POST['post_ID'];

        // clear data selected
        if( $this->check_clear_dates($post_id) ) return;
        
        $csv_file = get_field('upload_group_tour_dates_csv', $post_id);
        if( !$csv_file ) return;
        
        // Process csv file - save tour dates
        $this->process_dates_save($post_id, $csv_file);
    }

    public function check_clear_dates($post_id){
        $clear_dates = get_field('clear_dates_data', $post_id);
        if( $clear_dates ) {
            // Clear dates
            update_field( 'clear_dates_data', '', $post_id);
            update_field( 'group_tour_dates', '', $post_id);
            return true;
        }
        return false;
    }

    public function process_dates_save($post_id, $csv_file){

        //$o = '';
        //$o .= '<pre>'. print_r($csv_file, true) .'</pre>';

        $tour_dates = array();

        $tour_dates_string = '';

        if( ($handle = fopen($csv_file['url'], "r") ) !== FALSE) {

            $row = 0;
            
            // Loop through csv file
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { $row++;

                if( !$data[0] || $row == 1 )  continue;
                
                if( $row > 2 ) $tour_dates_string .= ',';

                $tour_dates_string .= implode('|', $data);

                //$o .= '<pre>'. print_r($data, true) .'</pre>';

                // Format = d/m/Y
                $date = $data[0];
                //$date = $this->format_date($date);

                $season = $data[1];
                $tour_dates[] = array(
                    'date' => $date,
                    'season' => $season,
                );

            } // loop

            $o .= '<pre>'. print_r($tour_dates_string, true) .'</pre>';
            //$o .= '<pre>'. print_r($tour_dates, true) .'</pre>';

            // Update tour dates field
            //update_field( 'group_tour_dates', $tour_dates, $post_id);

            update_field( 'group_tour_dates_picker', '', $post_id);
            update_field( 'group_tour_dates', $tour_dates_string, $post_id);

        } // handle
        
        //$o = '<pre>'. print_r($_POST, true) .'</pre>';

        //wp_die($o);
        
        // Clear upload field
        update_field( 'upload_group_tour_dates_csv', '', $post_id);
    }

    public function format_date($date) {
        $format_date = DateTime::createFromFormat('d/m/Y', $date);
        return $format_date->format('d F Y');
    }

}
$tour_dates_csv_upload = new TourDatesCsvUpload();