#!/usr/bin/env bash
# Deelt de projectkaarten uit als GitHub Issues:
#   push              -> Kaart 1, als er nog geen kaart is
#   /klaar op kaart   -> label "aftekenen" (de wachtrij van de docent)
#   docent sluit kaart -> volgende kaart
set -euo pipefail

K=.github/kaarten
LAATSTE=6
event=$(cat "$GITHUB_EVENT_PATH")
veld() { jq -r "$1" <<<"$event"; }

bestaat() {
    local titels
    titels=$(gh issue list --state all --limit 100 --json title --jq '.[].title')
    grep -q "^Kaart $1 " <<<"$titels"
}

thema_bestand() {
    local t
    t=$({ tr -d ' \r\n' < thema.txt; } 2>/dev/null) || return 1
    [ -n "$t" ] || return 1
    find "$K/userstories" -maxdepth 1 -iname "$t.md" | grep .
}

# Sectie k (1 = Bezoeker, 2 = klant, 3 = Medewerker, 4 = Admin, 5 = Developer)
userstories() {
    local f
    f=$(thema_bestand)
    printf '\n## Userstories\n\n'
    for k in "$@"; do
        awk -v k="$k" '/^### /{i++} i==k' "$f"
    done
}

open_kaart() {
    local n=$1
    bestaat "$n" && return 0
    {
        tail -n +2 "$K/$n.md"
        case $n in
            3) userstories 1 5 ;;
            4) userstories 2 ;;
            5) userstories 3 4 ;;
        esac
    } | gh issue create --title "$(head -1 "$K/$n.md" | sed 's/^# //')" --body-file -
}

# GitHub-gebruikersnamen zijn niet hoofdlettergevoelig
is_docent() {
    local docenten wie
    docenten=$(tr -d ' ' <<<",$DOCENTEN," | tr '[:upper:]' '[:lower:]')
    wie=$(tr '[:upper:]' '[:lower:]' <<<"$1")
    [[ $docenten == *",$wie,"* ]]
}

case $GITHUB_EVENT_NAME in
    push|workflow_dispatch)
        open_kaart 1
        ;;

    issue_comment)
        n=$(veld .issue.title | sed -n 's/^Kaart \([0-9]*\) .*/\1/p')
        [ -n "$n" ] && [ "$(veld .issue.state)" = open ] || exit 0
        [[ $(veld '.comment.body | split("\n")[0] | ascii_downcase') =~ ^\ */klaar ]] || exit 0
        nr=$(veld .issue.number)
        if ! thema_bestand >/dev/null; then
            gh issue comment "$nr" --body "Ik kan je thema niet vinden. Zet de naam van je thema (bijv. \`Camping\`) in \`thema.txt\`, push, en typ daarna opnieuw \`/klaar\`."
            exit 0
        fi
        gh label create aftekenen --color FBCA04 --description "Wacht op aftekenen door docent" --force
        gh issue edit "$nr" --add-label aftekenen
        gh issue comment "$nr" --body "Je staat in de wachtrij. Je docent komt langs om af te tekenen."
        ;;

    issues)
        n=$(veld .issue.title | sed -n 's/^Kaart \([0-9]*\) .*/\1/p')
        [ -n "$n" ] || exit 0
        : "${DOCENTEN:?Org-variabele DOCENTEN ontbreekt, zie .github/DOCENT.md}"
        nr=$(veld .issue.number)
        if ! is_docent "$(veld .sender.login)"; then
            gh issue reopen "$nr"
            gh issue comment "$nr" --body "Alleen een docent kan een kaart aftekenen. Klaar? Typ \`/klaar\`."
            exit 0
        fi
        gh issue edit "$nr" --remove-label aftekenen || true
        # Gesloten als "not planned" = niet afgetekend
        [ "$(veld .issue.state_reason)" = completed ] || exit 0
        if [ "$n" -lt "$LAATSTE" ]; then
            open_kaart $((n + 1))
        else
            gh issue comment "$nr" --body "Alle kaarten zijn afgetekend. Bereid je voor op het mondeling - zie de vragen op deze kaart."
        fi
        ;;
esac
