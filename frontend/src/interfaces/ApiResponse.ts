export interface ApiResponse<T> {
  success: boolean; // Indicates if the request was successful
  data: T | null;   // The actual data (or null in case of failure)
  message: string;  // A human-readable message
  errors?: string;  // Optional error details (if any)
}
