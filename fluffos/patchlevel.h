<<<<<<< HEAD
#define PATCH_LEVEL_PREFIX "v2.22-ds01-q"
=======
#define PATCH_LEVEL_PREFIX "v2.23-ds01"
>>>>>>> master
#ifdef __CYGWIN__
#define PATCH_LEVEL_SUFFIX "c"
#else
#ifndef WIN32
#define PATCH_LEVEL_SUFFIX ""
#else
#define PATCH_LEVEL_SUFFIX "w"
#endif
#endif
#define PATCH_LEVEL PATCH_LEVEL_PREFIX PATCH_LEVEL_SUFFIX
