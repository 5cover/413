/// @file
/// @author Raphaël
/// @brief Tchattator413 test - whois of 2147483647 by admin
/// @date 1/02/2025

#include "tests_tchattator413.h"
#include <tchattator413/tchattator413.h>

#define NAME admin_whois_imax

static void on_action(action_t const *action, void *t) {
    base_on_action(t);
    if (!test_case(t, action->type == action_type_whois, "type")) return;
    test_case_eq_uuid(t, action->with.whois.api_key, API_KEY_ADMIN, );
    test_case_eq_int(t, action->with.whois.user_id, 2147483647, );
}

static void on_response(response_t const *response, void *t) {
    base_on_response(t);
    test_case(t, !response->has_next_page, "");
    if (!test_case_eq_int(t, response->type, action_type_error, )) return;
    if (!test_case_eq_int(t, response->body.error.type, action_error_type_runtime, )) return;
    test_case_eq_int(t, response->body.error.info.runtime.status, status_not_found, );
}

TEST_SIGNATURE(NAME) {
    test_t test = { .t = test_start(STR(NAME)) };

    json_object *obj_input = json_object_from_file(IN_FILE(NAME, ));

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 1);

    test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, ));

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
