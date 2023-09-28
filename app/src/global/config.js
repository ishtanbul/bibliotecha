

export function getEndpoint() {
    return process.env.PROD_MODE === true ? "http://localhost:5000" : "http://localhost:80"
}

