/// @file
/// @author Raphaël
/// @brief Tchatator413 test - whois of 1 (by email) by member1
/// @date 1/02/2025

#include "tests_tchatator413.h"
#include <tchatator413/tchatator413.h>

#define NAME member1_whois_1_by_email

static void on_action(action_t const *action, void *t) {
    base_on_action(t);
    if (!test_case_eq_int(t, action->type, action_type_whois, "type")) return;
    test_case_eq_uuid(t, action->with.whois.api_key, API_KEY_MEMBER1, "api key");
    test_case_eq_int(t, action->with.whois.user_id, 1, "user id");
}

static void on_response(response_t const *response, void *t) {
    base_on_response(t);
    if (!test_case_eq_int(t, response->type, action_type_whois, "type")) return;
    test_case(t, !response->has_next_page, "");
    test_case_eq_int(t, response->body.whois.user.id, 1, "user id");
    test_case_eq_int(t, response->body.whois.user.kind, user_kind_pro_prive, "kind");
    test_case_eq_str(t, response->body.whois.user.display_name, "pro1", "display name");
    test_case_eq_str(t, response->body.whois.user.email, "pro@1.413", "email");
    test_case_eq_str(t, response->body.whois.user.first_name, "pro1_prenom", "first name");
    test_case_eq_str(t, response->body.whois.user.last_name, "pro1_nom", "last name");
}

TEST_SIGNATURE(NAME) {
    test_t test = { .t = test_start(STR(NAME)) };

    json_object *obj_input = load_json(IN_JSON(NAME, ));

    json_object *obj_output = tchatator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 1);

    test_output_json_file(&test, obj_output, OUT_JSON(NAME, ));

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
