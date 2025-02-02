#include <limits.h>
#include <stdarg.h>
#include <tchattator413/util.h>

char *strfmt(const char *fmt, ...) {
    va_list ap;
    va_start(ap, fmt);
    char *p = vstrfmt(fmt, ap);
    va_end(ap);
    return p;
}

char *vstrfmt(const char *fmt, va_list ap) {
    int n = 0;
    size_t size = 0;
    char *p = NULL;
    va_list ap_copy;
    va_copy(ap_copy, ap);

    /* Determine required size */

    n = vsnprintf(p, size, fmt, ap);

    if (n < 0)
        return NULL;

    /* One extra byte for '\0' */

    size = (size_t)n + 1;
    p = malloc(size);
    if (p == NULL)
        return NULL;

    n = vsnprintf(p, size, fmt, ap_copy);

    if (n < 0) {
        free(p);
        return NULL;
    }
    return p;
}

char *fslurp(FILE *fp) {
    char *answer;
    char *temp;
    int buffsize = 1024;
    int i = 0;
    int ch;

    answer = malloc(1024);
    if (!answer)
        return 0;
    while ((ch = fgetc(fp)) != EOF) {
        if (i == buffsize - 2) {
            if (buffsize > INT_MAX - 100 - buffsize / 10) {
                free(answer);
                return 0;
            }
            buffsize = buffsize + 100 * buffsize / 10;
            temp = realloc(answer, buffsize);
            if (temp == 0) {
                free(answer);
                return 0;
            }
            answer = temp;
        }
        answer[i++] = (char)ch;
    }
    answer[i++] = 0;

    temp = realloc(answer, i);
    if (temp)
        return temp;
    else
        return answer;
}
