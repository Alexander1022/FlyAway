import { Popup } from "react-leaflet";

interface CustomPopupProps {
  imageUrl: string;
  altText: string;
  contentText: string;
  date: Date;
  author: string;
}

export default function CustomPopup({
  imageUrl,
  altText,
  contentText,
  date,
  author,
}: CustomPopupProps) {
  return (
    <Popup>
      <div className="flex flex-col gap-2 min-w-[180px] max-w-[240px]">
        <div className="relative">
          <img
            src={imageUrl}
            alt={altText}
            className="w-full h-32 object-cover bg-gray-100 rounded-t"
            onError={(e) => {
              const target = e.target as HTMLImageElement;
              target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE0NCIgdmlld0JveD0iMCAwIDIwMCAxNDQiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMTQ0IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik84NSA2MEg5NVY3MEg4NVY6MCIgZmlsbD0iIzk5Q0EzQUYiLz4KPHBhdGggZD0iTTc1IDg1SDEyNVY5NUg3NVY4NVoiIGZpbGw9IiM5Q0EzQUYiLz4KPHN2Zz4K';
            }}
          />
          <span className="absolute top-1 left-1 text-xs bg-black/60 text-white rounded px-1">📸</span>
        </div>
        <div className="px-2 pb-2">
          <div className="text-base font-semibold text-gray-900 mb-1 truncate">
            {contentText || 'Unknown Species'}
          </div>
          <div className="flex items-center gap-2 text-xs text-gray-700 mb-1">
            <span className="bg-emerald-100 text-emerald-600 rounded-full w-5 h-5 flex items-center justify-center font-bold">
              {author.charAt(0).toUpperCase()}
            </span>
            <span className="truncate">{author}</span>
          </div>
          <div className="flex items-center gap-1 text-xs text-gray-500">
            <span>📅</span>
            <span>
              {date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
              })}
            </span>
          </div>
        </div>
      </div>
    </Popup>
  );
}