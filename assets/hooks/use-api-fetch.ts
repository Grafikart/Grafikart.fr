import {
  type UndefinedInitialDataOptions,
  useMutation,
  type UseMutationOptions,
  useQuery,
} from "@tanstack/react-query";

export async function apiFetch<T>(url: string, params?: RequestInit): Promise<T> {
  return fetch(url, {
    ...params,
    headers: {
      ...params?.headers,
      "X-Requested-With": "XMLHttpRequest",

      accept: "application/json",
      ...(typeof params?.body === "string"
        ? {
            "content-type": "application/json",
          }
        : {}),
    },
  }).then(async (response) => {
    // Expect empty response
    if (response.status === 204) {
      return null as unknown as Promise<T>;
    }
    // We are not receiving JSON
    if (!response.headers.get("content-type")?.includes("application/json")) {
      throw new APIError({ message: await response.text() }, response.status);
    }
    if (!response.ok) {
      throw new APIError(await response.json(), response.status);
    }
    return response.json();
  });
}

export class APIError extends Error {
  constructor(
    public data: Record<string, unknown>,
    public status: number,
  ) {
    super();
  }

  get message(): string {
    return "message" in this.data && typeof this.data.message === "string" ? this.data.message : "Server error";
  }

  get errors(): Record<string, string> {
    if (!("errors" in this.data)) {
      return {};
    }
    return this.data.errors as Record<string, string>;
  }
}

export function useApiFetch<T = unknown>(url: string, options?: Partial<UndefinedInitialDataOptions<T, APIError>>) {
  return useQuery<T, APIError>({
    ...options,
    queryKey: [url],
    queryFn: () => apiFetch<T>(url),
  });
}

export function useApiMutation<T = unknown, Body = Record<string, unknown> | FormData>(
  url: string,
  params: { method: RequestInit["method"] } = { method: "POST" },
  options: UseMutationOptions<T, APIError, Body, unknown> = {},
) {
  const mutation = useMutation<T, APIError, Body>({
    mutationFn: async (payload: Body) => {
      const isFormData = payload instanceof FormData;
      if (isFormData) {
        payload.append("_method", params.method ?? "POST");
      }
      return apiFetch<T>(url, {
        ...params,
        method: isFormData ? "POST" : params.method,
        body: payload instanceof FormData ? payload : JSON.stringify(payload),
      });
    },
    ...options,
  });
  return {
    ...mutation,
    error: mutation.error?.message,
    errors: mutation.error?.errors,
  };
}
