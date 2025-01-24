/// @file
/// @author Raphaël
/// @brief DAL - Interface
/// @date 23/01/2025

#ifndef DB_H
#define DB_H

#include "types.h"

/// @brief An opaque handle to a database connection.
typedef void db_t;

/// @brief Initialize a database connection.
/// @param verbosity The verbosity level.
/// @return A new database connection.
db_t *db_connect(int verbosity);

/// @brief Destroy a database connection.
/// @param db The database connection to destroy. No-op if @c NULL.
void db_destroy(db_t *db);

/// @brief Verify an API key.
/// @param db The database connection.
/// @param api_key The API key to verify.
/// @return @ref errstatus_ok - the API key is valid
/// @return @ref errstatus_error - the API key is invalid
/// @return @ref errstatus_handled - an error occured
errstatus_t db_verify_api_key(db_t *db, api_key_t api_key);

/// @brief Get the ID of an user from their e-mail.
/// @param db The database connection.
/// @param email The e-mail to look for.
/// @return The ID of the user with the specified e-mail.
/// @return @ref errstatus_t in case of failure.
serial_t db_get_user_id_by_email(db_t *db, const char *email);

/// @brief Get the ID of an user from their pseudo.
/// @param db The database connection.
/// @param pseudo The pseudo to look for.
/// @return The ID of the user with the specified pseudo.
/// @return @ref errstatus_t in case of failure.
serial_t db_get_user_id_by_pseudo(db_t *db, const char *pseudo);

/// @brief Fills a user record from its ID. If @p user->user_id is undefined, the behavior is undefined.
/// @param db The database connection.
/// @param user The user record to fill.
/// @return The error status of the operation.
errstatus_t db_get_user(db_t *db, user_t *user);

#endif // DB_H
