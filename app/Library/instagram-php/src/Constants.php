<?php

namespace InstagramAPI;

class Constants
{
    // Core API Constants.
    const API_URLS = [
        1 => 'https://i.instagram.com/api/v1/',
        2 => 'https://i.instagram.com/api/v2/',
        3 => 'https://graph.facebook.com/v2.3/',
        4 => 'https://graph.instagram.com/',
        5 => 'https://www.instagram.com/'
    ];
    const GRAPH_API_URL = 'https://graph.instagram.com/logging_client_events';

    const IG_VERSION = '188.0.0.35.124';
    const VERSION_CODE = '292080194';
    const IG_SIG_KEY = 'SIGNATURE';
    const EXPERIMENTS = 'ig_android_video_raven_streaming_upload_universe,ig_android_vc_explicit_intent_for_notification,ig_android_shopping_checkout_signaling,ig_stories_ads_delivery_rules,ig_shopping_checkout_improvements_universe,ig_business_new_value_prop_universe,ig_android_mqtt_cookie_auth_memcache_universe,ig_android_gallery_grid_controller_folder_cache,ig_android_suggested_users_background,ig_android_stories_music_search_typeahead,ig_android_direct_mutation_manager_media_3,ig_android_ads_bottom_sheet_report_flow,ig_android_login_onetap_upsell_universe,ig_fb_graph_differentiation,ig_android_shopping_bag_null_state_v1,ig_camera_android_feed_effect_attribution_universe,ig_android_test_not_signing_address_book_unlink_endpoint,ig_android_stories_share_extension_video_segmentation,ig_android_search_nearby_places_universe,ig_graph_management_production_h2_2019_holdout_universe,ig_android_vc_migrate_to_bluetooth_v2_universe,ig_ei_option_setting_universe,ig_android_test_remove_button_main_cta_self_followers_universe,instagram_ns_qp_prefetch_universe,ig_android_camera_leak,ig_android_separate_empty_feed_su_universe,ig_stories_rainbow_ring,ig_android_zero_rating_carrier_signal,ig_explore_2019_h1_destination_cover,ig_android_explore_recyclerview_universe,ig_android_image_pdq_calculation,ig_camera_android_subtle_filter_universe,ig_android_whats_app_contact_invite_universe,ig_android_direct_add_member_dialog_universe,ig_android_xposting_reel_memory_share_universe,ig_android_viewpoint_stories_public_testing,ig_graph_management_h2_2019_universe,ig_android_photo_creation_large_width,ig_android_save_all,ig_android_video_upload_hevc_encoding_universe,instagram_shopping_hero_carousel_visual_variant_consolidation,ig_android_vc_face_effects_universe,ig_android_fbpage_on_profile_side_tray,ig_android_ttcp_improvements,ig_android_igtv_refresh_tv_guide_interval,ig_shopping_bag_universe,ig_android_recyclerview_binder_group_enabled_universe,ig_android_video_exoplayer_2,ig_rn_branded_content_settings_approval_on_select_save,ig_android_account_insights_shopping_content_universe,ig_branded_content_tagging_approval_request_flow_brand_side_v2,ig_android_render_thread_memory_leak_holdout,ig_threads_clear_notifications_on_has_seen,ig_android_xposting_dual_destination_shortcut_fix,ig_android_show_create_content_pages_universe,ig_android_camera_reduce_file_exif_reads,ig_android_disk_usage_logging_universe,ig_android_stories_blacklist,ig_payments_billing_address,ig_android_fs_new_gallery_hashtag_prompts,ig_android_video_product_specific_abr,ig_android_sidecar_segmented_streaming_universe,ig_camera_android_gyro_senser_sampling_period_universe,ig_android_xposting_feed_to_stories_reshares_universe,ig_android_stories_layout_universe,ig_emoji_render_counter_logging_universe,ig_android_sharedpreferences_qpl_logging,ig_android_vc_cpu_overuse_universe,ig_android_image_upload_quality_universe,ig_android_invite_list_button_redesign_universe,ig_android_react_native_email_sms_settings_universe,ig_android_enable_zero_rating,ig_android_direct_leave_from_group_message_requests,ig_android_unfollow_reciprocal_universe,ig_android_publisher_stories_migration,aymt_instagram_promote_flow_abandonment_ig_universe,ig_android_whitehat_options_universe,ig_android_stories_context_sheets_universe,ig_android_stories_vpvd_container_module_fix,ig_android_delete_ssim_compare_img_soon,instagram_android_profile_follow_cta_context_feed,ig_android_personal_user_xposting_destination_fix,ig_android_stories_boomerang_v2_universe,ig_android_direct_message_follow_button,ig_android_video_raven_passthrough,ig_android_vc_cowatch_universe,ig_shopping_insights_wc_copy_update_android,android_cameracore_fbaudio_integration_ig_universe,ig_stories_ads_media_based_insertion,ig_android_analytics_background_uploader_schedule,ig_android_explore_reel_loading_state,ig_android_wellbeing_timeinapp_v1_universe,ig_end_of_feed_universe,ig_android_mainfeed_generate_prefetch_background,ig_android_feed_ads_ppr_universe,ig_android_igtv_browse_long_press,ig_xposting_mention_reshare_stories,ig_threads_sanity_check_thread_viewkeys,ig_android_vc_shareable_moments_universe,ig_android_igtv_stories_preview,ig_android_shopping_product_metadata_on_product_tiles_universe,ig_android_stories_quick_react_gif_universe,ig_android_video_qp_logger_universe,ig_android_stories_weblink_creation,ig_android_story_bottom_sheet_top_clips_mas,ig_android_frx_highlight_cover_reporting_qe,ig_android_vc_capture_universe,ig_android_optic_face_detection,ig_android_save_to_collections_flow,ig_android_direct_segmented_video,ig_android_stories_video_prefetch_kb,ig_android_direct_mark_as_read_notif_action,ig_android_not_interested_secondary_options,ig_android_product_breakdown_post_insights,ig_inventory_connections,ig_android_canvas_cookie_universe,ig_android_video_streaming_upload_universe,ig_android_smplt_universe,ig_android_vc_missed_call_call_back_action_universe,ig_cameracore_android_new_optic_camera2,ig_android_partial_share_sheet,ig_android_secondary_inbox_universe,ig_android_fbc_upsell_on_dp_first_load,ig_android_stories_sundial_creation_universe,saved_collections_cache_universe,ig_android_show_self_followers_after_becoming_private_universe,ig_android_music_story_fb_crosspost_universe,ig_android_payments_growth_promote_payments_in_payments,ig_carousel_bumped_organic_impression_client_universe,ig_android_business_attribute_sync,ig_biz_post_approval_nux_universe,ig_camera_android_bg_processor,ig_android_ig_personal_account_to_fb_page_linkage_backfill,ig_android_dropframe_manager,ig_android_ad_stories_scroll_perf_universe,ig_android_persistent_nux,ig_android_crash_fix_detach_from_gl_context,ig_android_tango_cpu_overuse_universe,ig_android_direct_wellbeing_message_reachability_settings,ig_android_edit_location_page_info,ig_android_unfollow_from_main_feed_v2,ig_android_stories_project_eclipse,ig_direct_android_bubble_system,ig_android_self_story_setting_option_in_menu,ig_android_frx_creation_question_responses_reporting,ig_android_li_session_chaining,ig_android_create_mode_memories_see_all,ig_android_feed_post_warning_universe,ig_mprotect_code_universe,ig_android_video_visual_quality_score_based_abr,ig_explore_2018_post_chaining_account_recs_dedupe_universe,ig_android_view_info_universe,ig_android_camera_upsell_dialog,ig_android_business_transaction_in_stories_consumer,ig_android_dead_code_detection,ig_android_stories_video_seeking_audio_bug_fix,ig_android_qp_kill_switch,ig_android_new_follower_removal_universe,ig_android_feed_post_sticker,ig_android_business_cross_post_with_biz_id_infra,ig_android_inline_editing_local_prefill,ig_android_reel_tray_item_impression_logging_viewpoint,ig_android_story_bottom_sheet_music_mas,ig_android_video_abr_universe,ig_android_unify_graph_management_actions,ig_android_vc_cowatch_media_share_universe,ig_challenge_general_v2,ig_android_place_signature_universe,ig_android_direct_inbox_cache_universe,ig_android_business_promote_tooltip,ig_android_wellbeing_support_frx_hashtags_reporting,ig_android_wait_for_app_initialization_on_push_action_universe,ig_android_direct_aggregated_media_and_reshares,ig_camera_android_facetracker_v12_universe,ig_android_story_bottom_sheet_clips_single_audio_mas,ig_android_fb_follow_server_linkage_universe,igqe_pending_tagged_posts,ig_sim_api_analytics_reporting,ig_android_self_following_v2_universe,ig_android_interest_follows_universe,ig_android_direct_view_more_qe,ig_android_audience_control,ig_android_memory_use_logging_universe,ig_android_branded_content_tag_redesign_organic,ig_camera_android_paris_filter_universe,ig_android_igtv_whitelisted_for_web,ig_rti_inapp_notifications_universe,ig_android_vc_join_timeout_universe,ig_android_share_publish_page_universe,ig_direct_max_participants,ig_commerce_platform_ptx_bloks_universe,ig_android_video_raven_bitrate_ladder_universe,ig_android_live_realtime_comments_universe,ig_android_recipient_picker,ig_android_graphql_survey_new_proxy_universe,ig_android_music_browser_redesign,ig_android_disable_manual_retries,ig_android_qr_code_nametag,ig_android_purx_native_checkout_universe,ig_android_fs_creation_flow_tweaks,ig_android_apr_lazy_build_request_infra,ig_android_business_transaction_in_stories_creator,ig_cameracore_android_new_optic_camera2_galaxy,ig_android_branded_content_appeal_states,ig_android_claim_location_page,ig_android_location_integrity_universe,ig_video_experimental_encoding_consumption_universe,ig_android_biz_story_to_fb_page_improvement,ig_shopping_checkout_improvements_v2_universe,ig_android_direct_thread_target_queue_universe,ig_android_save_to_collections_bottom_sheet_refactor,ig_android_branded_content_insights_disclosure,ig_android_create_mode_tap_to_cycle,ig_android_fb_profile_integration_universe,ig_android_shopping_bag_optimization_universe,ig_android_create_page_on_top_universe,android_ig_cameracore_aspect_ratio_fix,ig_android_feed_auto_share_to_facebook_dialog,ig_android_skip_button_content_on_connect_fb_universe,ig_android_igtv_explore2x2_viewer,ig_android_network_perf_qpl_ppr,ig_android_insights_post_dismiss_button,ig_xposting_biz_feed_to_story_reshare,ig_android_user_url_deeplink_fbpage_endpoint,ig_android_comment_warning_non_english_universe,ig_android_wellbeing_support_frx_cowatch_reporting,ig_android_stories_question_sticker_music_format,ig_promote_interactive_poll_sticker_igid_universe,ig_android_feed_cache_update,ig_pacing_overriding_universe,ig_explore_reel_ring_universe,ig_android_igtv_pip,ig_graph_evolution_holdout_universe,ig_android_wishlist_reconsideration_universe,ig_android_sso_use_trustedapp_universe,ig_android_stories_music_lyrics,ig_android_camera_formats_ranking_universe,ig_android_direct_multi_upload_universe,ig_android_stories_music_awareness_universe,ig_explore_2019_h1_video_autoplay_resume,ig_android_video_upload_quality_qe1,ig_android_expanded_xposting_upsell_directly_after_sharing_story_universe,ig_android_country_code_fix_universe,ig_android_stories_music_overlay,ig_android_multi_thread_sends,ig_android_render_output_surface_timeout_universe,ig_android_emoji_util_universe_3,ig_android_shopping_pdp_post_purchase_sharing,ig_branded_content_settings_unsaved_changes_dialog,ig_android_realtime_mqtt_logging,ig_android_rainbow_hashtags,ig_android_create_mode_templates,ig_android_direct_block_from_group_message_requests,ig_android_live_subscribe_user_level_universe,ig_android_video_call_finish_universe,ig_android_viewpoint_occlusion,ig_biz_growth_insights_universe,ig_android_logged_in_delta_migration,ig_android_push_reliability_universe,ig_android_self_story_button_non_fbc_accounts,ig_android_stories_gallery_video_segmentation,ig_android_explore_discover_people_entry_point_universe,ig_android_action_sheet_migration_universe,ig_android_live_webrtc_livewith_params,ig_camera_android_effect_metadata_cache_refresh_universe,ig_android_xposting_upsell_directly_after_sharing_to_story,ig_android_vc_codec_settings,ig_android_appstate_logger,ig_android_dual_destination_quality_improvement,ig_prefetch_scheduler_backtest,ig_android_ads_data_preferences_universe,ig_payment_checkout_cvv,ig_android_vc_background_call_toast_universe,ig_android_fb_link_ui_polish_universe,ig_android_qr_code_scanner,ig_disable_fsync_universe,mi_viewpoint_viewability_universe,ig_android_live_egl10_compat,ig_android_camera_gyro_universe,ig_android_video_upload_transform_matrix_fix_universe,ig_android_fb_url_universe,ig_android_reel_raven_video_segmented_upload_universe,ig_android_fb_sync_options_universe,ig_android_stories_gallery_sticker_universe,ig_android_recommend_accounts_destination_routing_fix,ig_android_enable_automated_instruction_text_ar,ig_traffic_routing_universe,ig_stories_allow_camera_actions_while_recording,ig_shopping_checkout_mvp_experiment,ig_android_video_fit_scale_type_igtv,ig_android_direct_state_observer,ig_android_igtv_player_follow_button,ig_android_arengine_remote_scripting_universe,ig_android_page_claim_deeplink_qe,ig_android_logging_metric_universe_v2,ig_android_xposting_newly_fbc_people,ig_android_recognition_tracking_thread_prority_universe,ig_android_contact_point_upload_rate_limit_killswitch,ig_android_optic_photo_cropping_fixes,ig_android_qpl_class_marker,ig_camera_android_gallery_search_universe,ig_android_sso_kototoro_app_universe,ig_android_vc_cowatch_config_universe,ig_android_profile_thumbnail_impression,ig_android_fs_new_gallery,ig_android_media_remodel,ig_camera_android_share_effect_link_universe,ig_android_igtv_autoplay_on_prepare,ig_android_ads_rendering_logging,ig_shopping_size_selector_redesign,ig_android_image_exif_metadata_ar_effect_id_universe,ig_android_optic_new_architecture,ig_android_external_gallery_import_affordance,ig_search_hashtag_content_advisory_remove_snooze,ig_android_on_notification_cleared_async_universe,ig_android_direct_new_gallery,ig_payment_checkout_info';
    const LOGIN_EXPERIMENTS = 'ig_android_reg_nux_headers_cleanup_universe,ig_android_device_detection_info_upload,ig_android_nux_add_email_device,ig_android_gmail_oauth_in_reg,ig_android_device_info_foreground_reporting,ig_android_device_verification_fb_signup,ig_android_direct_main_tab_universe_v2,ig_android_passwordless_account_password_creation_universe,ig_android_direct_add_direct_to_android_native_photo_share_sheet,ig_growth_android_profile_pic_prefill_with_fb_pic_2,ig_account_identity_logged_out_signals_global_holdout_universe,ig_android_quickcapture_keep_screen_on,ig_android_device_based_country_verification,ig_android_login_identifier_fuzzy_match,ig_android_reg_modularization_universe,ig_android_security_intent_switchoff,ig_android_video_render_codec_low_memory_gc,ig_android_device_verification_separate_endpoint,ig_android_suma_landing_page,ig_android_sim_info_upload,ig_android_smartlock_hints_universe,ig_android_fb_account_linking_sampling_freq_universe,ig_android_retry_create_account_universe,ig_android_caption_typeahead_fix_on_o_universe';

    const LAUNCHER_CONFIGS = 'ig_android_media_codec_info_collection,stories_gif_sticker,ig_android_felix_release_players,bloks_binding,ig_android_camera_network_activity_logger,ig_android_os_version_blocking_config,ig_android_carrier_signals_killswitch,live_special_codec_size_list,fbns,ig_android_aed,ig_client_config_server_side_retrieval,ig_android_bloks_perf_logging,ig_user_session_operation,ig_user_mismatch_soft_error,ig_android_prerelease_event_counter,fizz_ig_android,ig_android_vc_clear_task_flag_killswitch,ig_android_killswitch_perm_direct_ssim,ig_android_codec_high_profile,ig_android_smart_prefill_killswitch,sonar_prober,action_bar_layout_width,ig_auth_headers_device,always_use_server_recents';
    const LAUNCHER_LOGIN_CONFIGS = 'ig_camera_ard_use_ig_downloader,ig_android_dogfooding,ig_android_bloks_data_release,ig_donation_sticker_public_thanks,ig_business_profile_donate_cta_android,ig_launcher_ig_android_network_dispatcher_priority_decider_qe2,ig_multi_decode_config,ig_android_improve_segmentation_hint,ig_android_memory_manager_holdout,ig_android_interactions_direct_sharing_comment_launcher,ig_launcher_ig_android_analytics_request_cap_qe,ig_direct_e2e_send_waterfall_sample_rate_config,ig_android_cdn_image_sizes_config,ig_android_critical_path_manager,ig_android_mobileboost_camera,ig_android_pdp_default_sections,ig_android_video_playback,ig_launcher_explore_sfplt_secondary_response_android,ig_android_upload_heap_on_oom,ig_synchronous_account_switch,ig_android_direct_presence_digest_improvements,ig_android_request_compression_launcher,ig_android_feed_attach_report_logs,ig_android_insights_welcome_dialog_tooltip,ig_android_qp_surveys_v1,ig_direct_requests_approval_config,ig_android_react_native_ota_kill_switch,ig_android_video_profiler_loom_traces,video_call_gk,ig_launcher_ig_android_network_stack_cap_video_request_qe,ig_shopping_android_business_new_tagging_flow,ig_android_igtv_bitrate,ig_android_geo_gating,ig_android_explore_startup_prefetch,ig_android_camera_asset_blocker_config,post_user_cache_user_based,ig_android_branded_content_story_partner_promote_rollout,ig_android_quic,ig_android_videolite_uploader,ig_direct_message_type_reporting_config,ig_camera_android_whitelist_all_effects_in_pre,ig_android_shopping_influencer_creator_nux,ig_android_mobileboost_blacklist,ig_android_direct_gifs_killswitch,ig_android_global_scheduler_direct,ig_android_image_display_logging,ig_android_global_scheduler_infra,ig_igtv_branded_content_killswitch,ig_cg_donor_duplicate_sticker,ig_launcher_explore_verified_badge_on_ads,ig_android_cold_start_class_preloading,ig_camera_android_attributed_effects_endpoint_api_query_config,ig_android_highlighted_products_business_option,ig_direct_join_chat_sticker,ig_android_direct_admin_tools_requests,ig_android_rage_shake_whitelist,ig_android_shopping_ads_cta_rollout,ig_android_igtv_segmentation,ig_launcher_force_switch_on_dialog,ig_android_iab_fullscreen_experience_config,ig_android_instacrash,ig_android_specific_story_url_handling_killswitch,ig_mobile_consent_settings_killswitch,ig_android_influencer_monetization_hub_launcher,ig_and roid_scroll_perf_mobile_boost_launcher,ig_android_cx_stories_about_you,ig_android_replay_safe,ig_android_stories_scroll_perf_misc_fixes_h2_2019,ig_android_shopping_django_product_search,ig_direct_giphy_gifs_rating,ig_android_ppr_url_logging_config,ig_canvas_ad_pixel,ig_strongly_referenced_mediacache,ig_android_direct_show_threads_status_in_direct,ig_camera_ard_brotli_model_compression,ig_image_pipeline_skip_disk_config,ig_android_explore_grid_viewpoint,ig_android_iab_persistent_process,ig_android_in_process_iab,ig_android_launcher_value_consistency_checker,ig_launcher_ig_explore_peek_and_sfplt_android,ig_android_skip_photo_finish,ig_biz_android_use_professional_account_term,ig_android_settings_search,ig_android_direct_presence_media_viewer,ig_launcher_explore_navigation_redesign_android,ig_launcher_ig_android_network_stack_cap_api_request_qe,ig_qe_value_consistency_checker,ig_stories_fundraiser_view_payment_address,ig_business_create_donation_android,ig_android_qp_waterfall_logging,ig_android_bloks_demos,ig_redex_dynamic_analysis,ig_android_bug_report_screen_record,ig_shopping_android_carousel_product_ids_fix_killswitch,ig_shopping_android_creators_new_tagging_flow,ig_android_direct_threads_app_dogfooding_flags,ig_shopping_camera_android,ig_android_qp_keep_promotion_during_cooldown,ig_android_qp_slot_cooldown_enabled_universe,ig_android_request_cap_tuning_with_bandwidth,ig_android_client_config_realtime_subscription,ig_launcher_ig_android_network_request_cap_tuning_qe,ig_android_concurrent_coldstart,ig_android_gps_improvements_launcher,ig_android_notification_setting_sync,ig_android_stories_canvas_mode_colour_wheel,ig_android_iab_session_logging_config,ig_android_network_trace_migration,ig_android_extra_native_debugging_info,ig_android_insights_top_account_dialog_tooltip,ig_launcher_ig_android_dispatcher_viewpoint_onscreen_updater_qe,ig_android_disable_browser_multiple_windows,ig_contact_invites_netego_killswitch,ig_android_update_items_header_height_launcher,ig_android_bulk_tag_untag_killswitch,ig_android_employee_options,ig_launcher_ig_android_video_pending_request_store_qe,ig_story_insights_entry,ig_android_creator_multi_select,ig_android_direct_new_media_viewer,ig_android_gps_profile_launcher,ig_android_direct_real_names_launcher,ig_fev_info_launcher,ig_android_remove_request_params_in_network_trace,ig_android_rageshake_redesign,ig_launcher_ig_android_network_stack_queue_undefined_request_qe,ig_cx_promotion_tooltip,ig_text_response_bottom_sheet,ig_android_carrier_signal_timestamp_max_age,ig_android_qp_xshare_to_fb,ig_android_rollout_gating_payment_settings,ig_android_mobile_boost_kill_switch,ig_android_betamap_cold_start,ig_android_media_store,ig_android_async_view_model_launcher,ig_android_newsfeed_recyclerview,ig_android_feed_optimistic_upload,ig_android_fix_render_backtrack_reporting,ig_delink_lasso_accounts,ig_android_feed_report_ranking_issue,ig_android_shopping_insights_events_validator,ig_biz_android_new_logging_architecture,ig_launcher_ig_android_reactnative_realtime_ota,ig_android_boomerang_crash_android_go,ig_android_shopping_influencer_product_sticker_editing,ig_camera_android_max_vertex_texture_launcher,bloks_suggested_hashtag';
    const SIG_KEY_VERSION = '4';

    const IG_LOGIN_DEFAULT_ANDROID_PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvcu1KMDR1vzuBr9iYKW8\nKWmhT8CVUBRkchiO8861H7zIOYRwkQrkeHA+0mkBo3Ly1PiLXDkbKQZyeqZbspke\n4e7WgFNwT23jHfRMV/cNPxjPEy4kxNEbzLET6GlWepGdXFhzHfnS1PinGQzj0ZOU\nZM3pQjgGRL9fAf8brt1ewhQ5XtpvKFdPyQq5BkeFEDKoInDsC/yKDWRAx2twgPFr\nCYUzAB8/yXuL30ErTHT79bt3yTnv1fRtE19tROIlBuqruwSBk9gGq/LuvSECgsl5\nz4VcpHXhgZt6MhrAj6y9vAAxO2RVrt0Mq4OY4HgyYz9Wlr1vAxXXGAAYIvrhAYLP\n7QIDAQAB\n-----END PUBLIC KEY-----\n";
    const IG_LOGIN_DEFAULT_ANDROID_PUBLIC_KEY_ID = '41';
    const IG_LOGIN_ANDROID_PUBLIC_KEY= 'LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0KTUlJQklqQU5CZ2txaGtpRzl3MEJBUUVGQUFPQ0FROEFNSUlCQ2dLQ0FRRUF1enRZOEZvUlRGRU9mK1RkTGlUdAplN3FIQXY1cmdBMmk5RkQ0YjgzZk1GK3hheW14b0xSdU5KTitRanJ3dnBuSm1LQ0QxNGd3K2w3TGQ0RHkvRHVFCkRiZlpKcmRRWkJIT3drS3RqdDdkNWlhZFdOSjdLczlBM0NNbzB5UktyZFBGU1dsS21lQVJsTlFrVXF0YkNmTzcKT2phY3ZYV2dJcGlqTkdJRVk4UkdzRWJWZmdxSmsrZzhuQWZiT0xjNmEwbTMxckJWZUJ6Z0hkYWExeFNKOGJHcQplbG4zbWh4WDU2cmpTOG5LZGk4MzRZSlNaV3VxUHZmWWUrbEV6Nk5laU1FMEo3dE80eWxmeWlPQ05ycnF3SnJnCjBXWTFEeDd4MHlZajdrN1NkUWVLVUVaZ3FjNUFuVitjNUQ2SjJTSTlGMnNoZWxGNWVvZjJOYkl2TmFNakpSRDgKb1FJREFRQUIKLS0tLS1FTkQgUFVCTElDIEtFWS0tLS0tCg==';

    // Endpoint Constants.
    const BLOCK_VERSIONING_ID = 'e6bff5d5ad51ac25d872c2a0dd1b96d7eb4ba6021694601d6e2f803c7a81be6e'; // x-bloks-version-id	
    const BATCH_SURFACES = [
        ['5858', ['instagram_feed_tool_tip', 'instagram_navigation_tooltip']],
        ['4715', ['instagram_feed_header']],
        ['5734', ['instagram_feed_prompt', 'instagram_shopping_enable_auto_highlight_interstitial']],
        ['5858', ['instagram_fundraiser_sticker_tooltip']],
        ['5734', ['instagram_feed_prompt']],
        ['4715', ['instagram_profile_page']],
        ['5734', ['instagram_profile_page_prompt']],
        ['5734', ['instagram_other_profile_page_prompt']],
        ['4715', ['instagram_other_profile_page_header']],
        ['5858', ['instagram_other_profile_tooltip', 'instagram_other_checkout_profile_tooltip']]
    ];

    const BATCH_QUERY = 'Query QuickPromotionSurfaceQuery: Viewer {viewer() {eligible_promotions.trigger_context_v2(<trigger_context_v2>).ig_parameters(<ig_parameters>).trigger_name(<trigger_name>).surface_nux_id(<surface>).external_gating_permitted_qps(<external_gating_permitted_qps>).supports_client_filters(true).include_holdouts(true) {edges {client_ttl_seconds,log_eligibility_waterfall,is_holdout,priority,time_range {start,end},node {id,promotion_id,logging_data,max_impressions,triggers,contextual_filters {clause_type,filters {filter_type,unknown_action,value {name,required,bool_value,int_value,string_value},extra_datas {name,required,bool_value,int_value,string_value}},clauses {clause_type,filters {filter_type,unknown_action,value {name,required,bool_value,int_value,string_value},extra_datas {name,required,bool_value,int_value,string_value}},clauses {clause_type,filters {filter_type,unknown_action,value {name,required,bool_value,int_value,string_value},extra_datas {name,required,bool_value,int_value,string_value}},clauses {clause_type,filters {filter_type,unknown_action,value {name,required,bool_value,int_value,string_value},extra_datas {name,required,bool_value,int_value,string_value}}}}}},is_uncancelable,template {name,parameters {name,required,bool_value,string_value,color_value,}},creatives {title {text},content {text},footer {text},social_context {text},social_context_images,primary_action{title {text},url,limit,dismiss_promotion},secondary_action{title {text},url,limit,dismiss_promotion},dismiss_action{title {text},url,limit,dismiss_promotion},image.scale(<scale>) {uri,width,height}}}}}}}';
    
    const BATCH_SCALE = 2;
    const BATCH_VERSION = 1;

    // User-Agent Constants.
    const USER_AGENT_LOCALE = 'en_US'; // "language_COUNTRY".

    // HTTP Protocol Constants.
    const ACCEPT_LANGUAGE = 'en-US'; // "language-COUNTRY".
    const ACCEPT_ENCODING = 'gzip, deflate';
    const ACCEPT_ENCODING_GRAPH = 'gzip, deflate, br';
    const CONTENT_TYPE = 'application/x-www-form-urlencoded; charset=UTF-8';
    const X_FB_HTTP_Engine = 'Liger';
    const X_IG_Connection_Type = 'WIFI';
    const X_IG_Capabilities = '3brTvx0=';

    const APP_STARTUP_COUNTRY = 'RU';
    // const APP_STARTUP_COUNTRY = 'US'; // "COUNTRY".

    // Supported Capabilities
    const SUPPORTED_CAPABILITIES = [
        [
            'name'  => 'SUPPORTED_SDK_VERSIONS',
            'value' => '95.0,96.0,97.0,98.0,99.0,100.0,101.0,102.0,103.0,104.0,105.0,106.0,107.0,108.0,109.0,110.0,111.0,112.0,113.0,114.0',
        ],
        [
            'name'  => 'FACE_TRACKER_VERSION',
            'value' => '14',
        ],
        [
            'name'  => 'segmentation',
            'value' => 'segmentation_enabled',
        ],
        [
            'name'  => 'COMPRESSION',
            'value' => 'ETC2_COMPRESSION',
        ],
        [
            'name'  => 'world_tracker',
            'value' => 'world_tracker_enabled',
        ],
        [
            'name'  => 'gyroscope',
            'value' => 'gyroscope_enabled',
        ]
    ];

    // Facebook Constants.
    const FACEBOOK_OTA_FIELDS = 'update%7Bdownload_uri%2Cdownload_uri_delta_base%2Cversion_code_delta_base%2Cdownload_uri_delta%2Cfallback_to_full_update%2Cfile_size_delta%2Cversion_code%2Cpublished_date%2Cfile_size%2Cota_bundle_type%2Cresources_checksum%2Callowed_networks%2Crelease_id%7D';
    const FACEBOOK_ORCA_PROTOCOL_VERSION = 201150314;
    const FACEBOOK_ORCA_APPLICATION_ID = '124024574287414';
    const FACEBOOK_ANALYTICS_APPLICATION_ID = '567067343352427';
    const GRAPH_API_ACCESS_TOKEN = 'f249176f09e26ce54212b472dbab8fa8';

    // FACEBOOK_ANALYTICS_APPLICATION_ID|GRAPH_API_ACCESS_TOKEN
    const ANALYTICS_ACCESS_TOKEN = '567067343352427|f249176f09e26ce54212b472dbab8fa8';

    // MQTT Constants.
    const PLATFORM = 'android';
    const FBNS_APPLICATION_NAME = 'MQTT';
    const INSTAGRAM_APPLICATION_NAME = 'Instagram';
    const PACKAGE_NAME = 'com.instagram.android';

    // Instagram Quick Promotions.
    const SURFACE_PARAM = [
        4715,
        5734,
    ];

    // Internal Feedtype Constants. CRITICAL: EVERY value here MUST be unique!
    const FEED_TIMELINE = 1;
    const FEED_TIMELINE_ALBUM = 2;
    const FEED_STORY = 3;
    const FEED_DIRECT = 4;
    const FEED_DIRECT_STORY = 5;
    const FEED_TV = 6;

    // General Constants.
    const SRC_DIR = __DIR__; // Absolute path to the "src" folder.

    // Story view modes.
    const STORY_VIEW_MODE_ONCE = 'once';
    const STORY_VIEW_MODE_REPLAYABLE = 'replayable';
    const STORY_VIEW_MODE_PERMANENT = 'permanent';

    // Checkpoint challenge choices to receive the code
    const CHALLENGE_CHOICE_SMS = 0;
    const CHALLENGE_CHOICE_EMAIL = 1;

    const IG_WEB_APPLICATION_ID = '936619743392459';
    const WEB_CLIENT_TOKEN = '3cdb3f896252a1db29679cb4554db266';
    // IG_WEB_APPLICATION_ID|WEB_CLIENT_TOKEN
    const WEB_ANALYTICS_ACCESS_TOKEN = '936619743392459|3cdb3f896252a1db29679cb4554db266';

    const X_IG_WWW_CLAIM = 'hmac.AR3e5w_LxLRDjxoSDYnlijgOwTGZ6fsRSI61EuaDvx5JsbLf';

    // iOS API constants
    // Instagram 187.0.0.32.120 (iPhone10,2; iOS 13_5_1; en_US; en-US; scale=2.61; 1080x1920; 289678855)
    const IG_IOS_VERSION      = '187.0.0.32.120';
    const IOS_VERSION         = '13_5_1';
    const IOS_MODEL           = 'iPhone10,2';
    const IOS_DPI             = '1080x1920';
    const IOS_SCALE           = '2.61';
    const IG_IOS_VERSION_CODE = '289678855';
    const SIG_KEY_IOS_VERSION = '5';
    const IOS_IG_SIG_KEY      = 'SIGNATURE';
    const IOS_BLOCKS_VERSIONING_ID = 'e6bff5d5ad51ac25d872c2a0dd1b96d7eb4ba6021694601d6e2f803c7a81be6e';
    const IOS_X_IG_Capabilities = '3brTvx0=';

    // License
    const LICENSE_KEY = 'YOUR-LICENSE-KEY';
}