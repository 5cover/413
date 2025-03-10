/// @file
/// @author Raphaël
/// @brief json-c wrapper. Include this instead of JSON-C (compile time error mechanisms)
/// @date 03/02/2025

#ifndef JSON_C_H
#define JSON_C_H

#include <json-c/json.h>

// Some error-catching mechanisms
#ifdef __GNUC__
#define json_object_new_int(i) __extension__({_Static_assert(sizeof (i) == sizeof (int32_t), "use json_object_new_int64"); json_object_new_int(i); })
#define json_object_new_int64(i) __extension__({_Static_assert(sizeof (i) == sizeof (int64_t), "use json_object_new_int"); json_object_new_int64(i); })
#endif

#endif // JSON_C_H
