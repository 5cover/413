#include <stdio.h>
#include "../lib/json-c/json.h"

int main() {
    printf("Version: %s\n", json_c_version());
    printf("Version Number: %d\n", json_c_version_num());
    return 0;
}
