<?php 
    // Supabase Connection Details
    $host = "db.ckfsjobfcpyamxejnjbu.supabase.co";
    $port = 6543;
    $dbname = "postgres";
    $user = "postgres";
    $password = "Shivam1105#";

    // Explicitly use the resolved IPv6 address as DNS is only returning IPv6
    // Note: IPv6 addresses must be enclosed in square brackets for PostgreSQL connection strings
    $host = "[2406:da1c:f42:ae01:e710:acc4:edfe:af3]"; 
    
    // Fallback to hostname if IPv6 fails for some reason or changes
    // $host = "db.ckfsjobfcpyamxejnjbu.supabase.co"; 

    $connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require";

    // Establish the PostgreSQL connection
    // Establish the PostgreSQL connection using @ to suppress warnings
    $conn = @pg_connect($connection_string);

    if (!$conn) {
        $error = error_get_last();
        $errorMessage = isset($error['message']) ? $error['message'] : 'Unknown error';
        
        // Clean up the error message for display
        $errorMessage = str_replace("pg_connect(): ", "", $errorMessage);

        die("
            <div style='font-family: sans-serif; padding: 20px; border: 1px solid #f44336; background: #ffebee; border-radius: 5px;'>
                <h3 style='color: #d32f2f; margin-top: 0;'>Database Connection Failed</h3>
                <p><strong>Error:</strong> " . htmlspecialchars($errorMessage) . "</p>
                <hr style='border: 0; border-top: 1px solid #ffcdd2; margin: 15px 0;'>
                <strong>Troubleshooting Steps:</strong>
                <ul>
                    <li><strong>Check Project Status:</strong> Ensure your Supabase project (ID: ckfsjobfcpyamxejnjbu) is not <em>paused</em> in the dashboard.</li>
                    <li><strong>Verify VPN/Network:</strong> Some networks block port $port. Try using a different network or VPN.</li>
                    <li><strong>Check Hostname:</strong> Your computer cannot resolve 'db.ckfsjobfcpyamxejnjbu.supabase.co'. This is often a DNS issue.</li>
                </ul>
            </div>
        ");
    }
?>