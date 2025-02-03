/// @file
/// @author Raphaël
/// @brief DAL - Interface
/// @date 23/01/2025

#ifndef DB_H
#define DB_H

#include "errstatus.h"
#include "types.h"

/// @brief An opaque handle to a database connection.
typedef void db_t;

typedef struct {
    void *memory_owner_db;
    serial_t id;
    user_kind_t kind;
    char const *email, *last_name, *first_name, *display_name;
} user_t;

typedef struct {
    void *memory_owner_db;
    msg_t *msgs;
    size_t n_msgs;
} msg_list_t;

/// @brief Initialize a database connection.
/// @param verbosity The verbosity level.
/// @return A new database connection.
/// @return @ref NULL if the connection failed.
db_t *db_connect(int verbosity, char const *host, char const *port, char const *database, char const *username, char const *password);

/// @brief Destroy a database connection.
/// @param db The database connection to destroy. No-op if @c NULL.
void db_destroy(db_t *db);

/// @brief Cleans up a memory owner.
/// @note @c NULL is no-op
void db_collect(void *memory_owner);

/// @brief Verify an API key.
/// @param db The database.
/// @param out_user Assigned to the identity of the user.
/// @param api_key The API key to verify.
/// @return @ref errstatus_handled A database error occured. A message has been shown. @p out_user is untouched.
/// @return @ref errstatus_error The API key isn't valid. @p out_user is untouched.
/// @return @ref errstatus_ok The API key is valid.
errstatus_t db_verify_user_api_key(db_t *db, user_identity_t *out_user, api_key_t api_key);

/// @brief Get the ID of an user from their e-mail.
/// @param db The database.
/// @param email The e-mail to look for.
/// @return The ID of the user with the specified e-mail.
/// @return @ref errstatus_handled A database error occured. A message has been shown. @p out_user is untouched.
/// @return @ref errstatus_error No user of e-mail @p email exists in the database.
serial_t db_get_user_id_by_email(db_t *db, char const *email);

/// @brief Get the ID of an user from their name.
/// @param db The database.
/// @param name The name to look for. It can be a member's pseudo or a pro's display name
/// @return The ID of the user with the specified name.
/// @return @ref errstatus_handled A database error occured. A message has been shown. @p out_user is untouched.
/// @return @ref errstatus_error No user of name @p name exists in the database.
serial_t db_get_user_id_by_name(db_t *db, char const *name);

/// @brief Fills a user record from its ID. If @p user->user_id is undefined, the behavior is undefined.
/// @param db The database.
/// @param user The user record to fill.
/// @return @ref errstatus_handled A database error occured. A message has been shown. @p user is untouched.
/// @return @ref errstatus_error No user of ID @p user->user_id exists in the database. @p user is untouched.
/// @return @ref errstatus_ok Success.
errstatus_t db_get_user(db_t *db, user_t *user);

/// @brief Check a password against the stored hash for an user.
/// @param db The database.
/// @param user_id The ID of the user to check the password of. Can be @c 0 for the administrator.
/// @param password The clear password to check.
/// @return @ref errstatus_ok The password matched.
/// @return @ref errstatus_error The password didn't match or no user of ID @p user_id exists in the database.
/// @return @ref errstatus_handled On database error (handled).
errstatus_t db_check_password(db_t *db, serial_t user_id, char const *password);

/// @brief Get the role of an user.
/// @param db The database.
/// @param user_id The ID of the user to get the role of.
/// @return @ref role_flags_t the role of the user is found.
/// @return @ref errstatus_handled A database error occured. A message has been shown. @p out_user is untouched.
/// @return @ref errstatus_error No user of ID @p user_id exists in the database.
int db_get_user_role(db_t *db, serial_t user_id);

/// @brief Counts the amount of messages sent from sender to a recipient
/// @param db The database.
/// @param sender_id The ID of the sender user of the messages. (@c 0 for adminitrator)
/// @param recipient_id The ID of the recipient user of the message.
/// @return The counts of message from @p sender_id to @p recipient_id.
/// @return @ref errstatus_handled A database error occured. A message has been shown.
int db_count_msg(db_t *db, serial_t sender_id, serial_t recipient_id);

/// @brief Sends a message.
/// @param db The database.
/// @param sender_id The ID of the sender user
/// @param recipient_id The ID of the recipient user
/// @param content The null-terminated string containing the content of the message.
/// @return @ref The message ID.
/// @return @ref errstatus_handled A database error occured. A message has been shown. @p out_user is untouched.
/// @return @ref errstatus_error The sender has been blocked from sending messages, either globally or by this particular recipient.
serial_t db_send_msg(db_t *db, serial_t sender_id, serial_t recipient_id, char const *content);

/// @brief Creates an array with the messages an user has recieved, sorted by sent/edited date, in reverse chronological order.
/// @param db The database.
/// @param limit The maximum number of messages to fetch.
/// @param offset The offset of the query.
/// @param recipient_id The ID of the user who recieved the messages.
/// @return A message list. The memory owner is @c NULL on error.
msg_list_t db_get_inbox(db_t *db,
    int32_t limit,
    int32_t offset,
    serial_t recipient_id);

errstatus_t db_get_msg(db_t *db, msg_t *msg, void **memory_owner_db);

errstatus_t db_rm_msg(db_t *db, serial_t msg_id);

#endif // DB_H
