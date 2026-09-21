export default function Button({ variant = "primary", children, ...props }) {
  const styles = {
    primary: "bg-blue-600 text-white",
    danger: "bg-red-600 text-white",
    ghost: "bg-transparent text-slate-600 border",
  };
  return (
    <button
      className={`rounded px-3 py-1 text-sm ${styles[variant]}`}
      {...props}
    >
      {children}
    </button>
  );
}
