#[no_mangle]
pub extern "C" fn rust_hello() -> *mut u8 {
    let message = "Hello from Rust!";
    // Convert to C string and leak memory (since we're returning to C/PHP)
    let c_str = std::ffi::CString::new(message).unwrap();
    c_str.into_raw() as *mut u8
}