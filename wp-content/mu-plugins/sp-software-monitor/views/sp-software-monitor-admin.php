<div class="wrap genesis-metaboxes" id="synthesis-software-monitor">
    <?php screen_icon( 'synthesis-management' ); ?>

    <h2><?php _e( 'Software Monitor' ); ?></h2>

    <div class="metabox-holder">
        <div class="postbox-container">
            <div id="main-sortables" class="meta-box-sortables ui-sortable">
                <!-- Synthesis disk quota monitor -->
                <?php
                    if ( IS_SYNTHESIS ) {
                        Synthesis_Resource_Monitor::disk_quota_markup();
                    }
                ?>
                
                <!-- Update options -->
                <div id="update-options" class="postbox">
                    <h2 class='hndle'><span>WordPress Updates</span></h2>
                    <div class="inside">
                    <table class="form-table">
                    <tbody>
                        <tr>
                        <th scope="row"><?php _e( 'WordPress Core Updates' ); ?></th>
                            <td>
                                <?php Synthesis_Software_Monitor::wp_core_updates_markup(); ?>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                    </div>
                </div>
                <!-- HTTPS options -->
                <div id="https-options" class="postbox">
                    <h2 class='hndle'><span>HTTPS Settings</span></h2>
                    <div class="inside">
                    <table class="form-table">
                    <tbody>
                        <tr>
                        <th scope="row"><?php _e( 'HTTPS Option' ); ?></th>
                            <td>
                                <?php Synthesis_Software_Monitor::sp_has_ssl_markup(); ?>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                    </div>
                </div>
                <!-- Theme options -->
                <div id="theme-options" class="postbox">
                    <h2 class='hndle'><span>Theme Options</span></h2>
                    <div class="inside">
                    <table class="form-table">
                    <tbody>
                    <tr>
                    <th scope="row"><?php _e( 'Theme Monitor' ); ?></th>
                        <td>
                            <?php Synthesis_Software_Monitor::theme_monitor_markup(); ?>
                        </td>
                    </tr>
                    <tr>
                    <th scope="row"><?php _e( 'Theme Snapshots' ); ?></th>
                        <td>
                            <p><?php
                                _e( 'Take snapshot of your current active and inactive themes as a reference. This is a report only function and DOES NOT save themes files or settings.' );
                                printf( __( 'You can store up to %d snapshots.' ), Synthesis_Software_Monitor::SNAPSHOT_LIMIT );
                            ?></p>

                            <p><form action="" method="post"><button class="button" id="archive-inactive-themes">New Theme Snapshot</button></form></p>

                            <p><?php _e( 'Your snapshot reports are listed here. Click a snapshot report title to show report details.' ); ?></p>
                            <div id="theme-snapshots" class="smash-container">
                                <?php Synthesis_Software_Monitor::theme_snapshots_markup(); ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    </table>
                    </div>
                </div>
                <!-- Plugin options -->
                <div id="plugin-options" class="postbox">
                    <h2 class='hndle'><span>Plugin Options</span></h2>
                    <div class="inside">
                    <table class="form-table">
                    <tbody>
                    <tr>

                    <th scope="row"><?php _e( 'Plugin Monitor' ); ?></th>
                        <td>
                            <?php Synthesis_Software_Monitor::plugin_monitor_markup(); ?>
                        </td>
                    </tr>
                    <tr>
                    <th scope="row"><?php _e( 'Plugin Snapshots' ); ?></th>
                        <td>
                            <p><?php
                                _e( 'Take snapshot of your current active and inactive plugins as a reference. This is a report only function and DOES NOT save plugin files or settings.' );
                                printf( __( 'You can store up to %d snapshots.' ), Synthesis_Software_Monitor::SNAPSHOT_LIMIT );
                            ?></p>

                            <p><form action="" method="post"><button class="button" id="archive-inactive-plugins">New Plugin Snapshot</button></form></p>

                            <p><?php _e( 'Your snapshot reports are listed here. Click a snapshot report title to show report details.' ); ?></p>

                            <div id="plugin-snapshots" class="smash-container">
                                <?php Synthesis_Software_Monitor::plugin_snapshots_markup(); ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>  

</div>